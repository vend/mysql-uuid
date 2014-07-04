# MySQL UUIDs

This is a small library for working with MySQL UUIDs in PHP. MySQL UUIDs are
128-bit values returned from the `UUID()` command in MySQL, and by default
are formatted as a hex-and-dash traditional UUID string, like this:

```
02fbfeee-02ff-11e4-9583-080027f3add4
```

There are a couple of problems with using this sort of value directly:

 * The string stores the most frequently changing timestamp field at the start (that is, it's little-endian);
   using this value directly will spread writes all across your tablespace and
   indexes, ruining any sort of locality. We want the *last* part of the string to contain
   the most frequently changing part of the UUID value.
 * The string can't be directly used in a 128-bit column, such as `BINARY(16)`;
   storing the string directly would take about 37 bytes rather than 16, bloating
   indexes and row sizes.

MySQL UUIDs are generated according to "[DCE 1.1: Remote Procedure Call](http://www.opengroup.org/public/pubs/catalog/c706.htm)" (Appendix A) CAE (Common Applications Environment) Specifications published by The Open Group in October 1997 (Document Number C706).

## Why Do This?

Mainly for data clustering. We don't want a single buffer pool page to be under a lot of pressure for inserts (as would be the case with an auto_increment column), and we also don't want to randomly spread data across the entire index/table.

## Supported Formats

This library uses `pack()` and `unpack()` to munge MySQL UUID values into ones more suitable
for use as a database key. The supported formats are:

 * **Standard UUID strings**, with dashes.
 * **Hex UUIDs**, which are just like string UUIDs, but without the field separator dashes - perhaps useful for using with MySQL's `HEX()` and `UNHEX()`
 * **Binary UUIDs**, which are 16 byte binary strings with just the underlying 128-bit UUID value, no formatting at all

## Quick API Example

```php
<?php

use MysqlUuid\Uuid;
use MysqlUuid\Formats\Binary;

$uuid = new Uuid('02fbfeee-02ff-11e4-9583-080027f3add4');

$hex       = $uuid->toFormat(new Hex(false));    // string: 02fbfeee02ff11e49583080027f3add4
$reordered = $uuid->toFormat(new String(true));  // string: 080027f3-add4-11e4-9583-02ff02fbfeee
$binary    = $uuid->toFormat(new Binary());      // string: \x08\x00\x27\xf3\xad\xd4\x95\x83\x11\xe4\x02\xff\x02\xfb\xfe\xee
```

## Field Reordering

Here's how the default MySQL-produced string UUID is built out of fields:

Field                     | Type           | Octet | Note
-----                     | ----           | ----- | ----
time_low                  | unsigned long  | 0-3   | The low field of the timestamp.
time_mid                  | unsigned short | 4-5   | The middle field of the timestamp.
time_hi_and_version       | unsigned short | 6-7   | The high field of the timestamp multiplexed with the version number.
clock_seq_hi_and_reserved | unsigned small | 8     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small | 9     | The low field of the clock sequence.
node                      | character      | 10-15 | The spatially unique node identifier.

See what is meant by "ruins locality"? The fact `time_low` is the first field basically distributes your keys randomly over the possible ID space. When you pass the `reorder` flag to either the string or hex formats, you'll instead get the following field order:

Field                     | Type            | Octet | Note
-----                     | ----            | ----- | ----
node_high                 | character       | 0-3   | The high field of the spatially unique node identifier.
node_low                  | character       | 4-5   | The low field of the spatially unique node identifier.
time_hi_and_version       | unsigned short  | 6-7   | The high field of the timestamp multiplexed with the version number.
clock_seq_hi_and_reserved | unsigned small  | 8     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small  | 9     | The low field of the clock sequence.
time_midlow               | unsigned 48-bit | 10-15 | The mid field (long) of the timestamp multiplexed with the low field (short) of the timestamp.

Note that we leave the version and variant fields in the same place. When we reorder, we change the variant to `3` and set the version to `3`, which allows us to later identify reordered UUIDs.

For binary IDs, we always reorder the fields, and there's no standard way to read the variant/version anyway (no field separators), so we rearrange the fields more aggressively (no need to keep the 4-2-2-2-6 format):

Field                     | Type           | Octet | Note
-----                     | ----           | ----- | ----
node                      | character      | 0-5   | The spatially unique node identifier.
clock_seq_hi_and_reserved | unsigned small | 6     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small | 7     | The low field of the clock sequence.
time_hi_and_version       | unsigned short | 8-9   | The high field of the timestamp multiplexed with the version number.
time_mid                  | unsigned short | 10-11 | The middle field of the clock timestamp.
time_low                  | unsigned long  | 12-15 | The low field of the timestamp.
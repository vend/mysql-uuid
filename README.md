# MySQL UUIDs

[![Build Status](https://travis-ci.org/vend/mysql-uuid.png)](https://travis-ci.org/vend/mysql-uuid)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/vend/mysql-uuid/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/vend/mysql-uuid/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/vend/mysql-uuid/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/vend/mysql-uuid/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/vend/mysql-uuid/v/stable.svg)](https://packagist.org/packages/vend/mysql-uuid)
[![License](https://poser.pugx.org/vend/mysql-uuid/license.svg)](https://packagist.org/packages/vend/mysql-uuid)

## Description

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

## Installation

Install via composer. That's it.

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

$hex       = $uuid->toFormat(new Hex());        // string: 02fbfeee02ff11e49583080027f3add4
$reordered = $uuid->toFormat(new Reordered());  // string: 080027f3-add4-11e4-9583-02ff02fbfeee
$binary    = $uuid->toFormat(new Binary());     // string: \x08\x00\x27\xf3\xad\xd4\x95\x83\x11\xe4\x02\xff\x02\xfb\xfe\xee
```

## Field Reordering

### String and Hex

Here's how the default MySQL-produced string UUID is built out of fields. We use this field structure for both the regular 'string' format, and the 'hex' format.

Field                     | Type           | Octet | Note
-----                     | ----           | ----- | ----
time_low                  | unsigned long  | 0-3   | The low field of the timestamp.
time_mid                  | unsigned short | 4-5   | The middle field of the timestamp.
time_hi_and_version       | unsigned short | 6-7   | The high field of the timestamp multiplexed with the version number.
clock_seq_hi_and_reserved | unsigned small | 8     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small | 9     | The low field of the clock sequence.
node                      | character      | 10-15 | The spatially unique node identifier.

See what is meant by "ruins locality"? The fact `time_low` is the first field basically distributes your keys randomly over the possible ID space.

### Reordered String

If you use the 'reordered' format, you'll instead get a UUID with this field format:

Field                     | Type            | Octet | Note
-----                     | ----            | ----- | ----
node_high                 | character       | 0-3   | The high field of the spatially unique node identifier.
node_low                  | character       | 4-5   | The low field of the spatially unique node identifier.
time_hi_and_version       | unsigned short  | 6-7   | The high field of the timestamp multiplexed with the version number.
clock_seq_hi_and_reserved | unsigned small  | 8     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small  | 9     | The low field of the clock sequence.
time_midlow               | unsigned 48-bit | 10-15 | The mid field (long) of the timestamp multiplexed with the low field (short) of the timestamp.

Note that we leave the version and variant fields in the same place. This means if you have anything that gets information from the version and variant fields, or does something like parse the timestamp fields back out of the ID, then this format won't be backward compatible for you. Ideally, we'd set a different version or variant, but it's unclear which values are reserved (e.g. Microsoft has an assigned variant for their UUIDs), and this would still cause problems if we picked a variant already in use.

### Binary

For binary UUIDs, we always reorder the field, and in a slightly more aggressive way than the reordered string format. There's no standard way to read the variant/version from a 16 byte binary value, and no field separators, so we rearrange the fields more aggressively (no need to keep the 4-2-2-2-6 byte format):

Field                     | Type           | Octet | Note
-----                     | ----           | ----- | ----
node                      | character      | 0-5   | The spatially unique node identifier.
clock_seq_hi_and_reserved | unsigned small | 6     | The high field of the clock sequence multiplexed with the variant.
clock_seq_low             | unsigned small | 7     | The low field of the clock sequence.
time_hi_and_version       | unsigned short | 8-9   | The high field of the timestamp multiplexed with the version number.
time_mid                  | unsigned short | 10-11 | The middle field of the clock timestamp.
time_low                  | unsigned long  | 12-15 | The low field of the timestamp.

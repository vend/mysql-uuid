# MySQL UUID
## Library for PHP

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
 * The string can't be directly used in a 128-bit column, such as BINARY(16);
   storing the string directly would take about 37 bytes rather than 16, bloating
   indexes and row sizes.

MySQL UUIDs are generated according to "[DCE 1.1: Remote Procedure Call](http://www.opengroup.org/public/pubs/catalog/c706.htm)" (Appendix A) CAE (Common Applications Environment) Specifications published by The Open Group in October 1997 (Document Number C706).

## Supported Formats

This library uses `pack()` and `unpack()` to munge MySQL UUID values into ones more suitable
for use as a database key. The supported formats are:

 * **Standard UUID strings**, with dashes.
 * **Hex UUIDs**, which are just like string UUIDs, but without the field separator dashes
 * **Binary UUIDs**, which are 16 byte binary strings with just the underlying 128-bit UUID value, no formatting at all

## Field Formats

Any of the UUID formats can be optionally reordered to a big-endian value. This 

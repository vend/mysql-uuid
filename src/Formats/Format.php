<?php

namespace MysqlUuid\Formats;

/**
 * Represents a format of a UUID type
 */
interface Format
{
    /**
     * Whether the given value appears to fit this format
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value);

    /**
     * Converts a formatted value to a set of fields
     *
     * Fields are always returned as hex strings
     *
     * @param string $value
     * @return array<string,string>
     */
    public function toFields($value);

    /**
     * Converts a set of fields to a formatted value
     *
     * @param array<string,string> $fields
     * @return string
     */
    public function fromFields(array $fields);
}

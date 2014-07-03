<?php

namespace MysqlUuid\Formats;

/**
 * Hex with dashes removed
 */
class Hex implements Format
{
    /**
     * Whether the given value appears to fit this format
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        return (boolean)preg_match('/[0-9A-F]{32}/i', $value);
    }

    /**
     * Converts a formatted value to a set of fields
     *
     * @param string $value
     * @return array<string,string>
     */
    public function toFields($value)
    {
        // TODO: Implement toFields() method.
    }

    /**
     * Converts a set of fields to a formatted value
     *
     * @param array <string,string> $fields
     * @return string
     */
    public function fromFields(array $fields)
    {


        // TODO: Implement fromFields() method.
    }
}

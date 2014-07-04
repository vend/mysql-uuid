<?php

namespace MysqlUuid\Formats;

/**
 * Hex with dashes removed, a 32 byte string of hex characters
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
        return [
            'time_low'  => substr($value, 0, 8),
            'time_mid'  => substr($value, 8, 4),
            'time_high' => substr($value, 12, 4),
            'clock_seq' => substr($value, 16, 4),
            'node'      => substr($value, 20, 12)
        ];
    }

    /**
     * Converts a set of fields to a formatted value
     *
     * @param array <string,string> $fields
     * @return string
     */
    public function fromFields(array $fields)
    {
        return sprintf(
            '%s%s%s%s%s',
            $fields['time_low'],
            $fields['time_mid'],
            $fields['time_high'],
            $fields['clock_seq'],
            $fields['node']
        );
    }
}

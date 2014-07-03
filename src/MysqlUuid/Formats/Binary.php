<?php

namespace MysqlUuid\Formats;

/**
 * 16 byte binary format
 */
class Binary implements Format
{
    const PACK   = 'H12H4H4H4H8';
    const UNPACK = 'H12node/H4clock_seq/H4time_high/H4time_mid/H8time_low';

    /**
     * Whether the given value appears to fit this format
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        return (strlen($value) == 16);
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
        return pack(
            self::PACK,
            $fields['node'],
            $fields['clock_seq'],
            $fields['time_high'],
            $fields['time_mid'],
            $fields['time_low']
        );
    }
}

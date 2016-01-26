<?php

namespace MysqlUuid\Formats;

/**
 * The traditional UUID string format
 */
class PlainString implements Format
{
    const FORMAT = '/[0-9A-F]{8}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{4}-[0-9A-F]{12}/i';

    /**
     * Whether the given value appears to fit this format
     *
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        return (boolean)preg_match(self::FORMAT, $value);
    }

    /**
     * Converts a formatted value to a set of fields
     *
     * @param string $value
     * @return array<string,string>
     */
    public function toFields($value)
    {
        return array_combine(['time_low', 'time_mid', 'time_high', 'clock_seq', 'node'], explode('-', $value));
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
            '%s-%s-%s-%s-%s',
            $fields['time_low'],
            $fields['time_mid'],
            $fields['time_high'],
            $fields['clock_seq'],
            $fields['node']
        );
    }
}

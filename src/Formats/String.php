<?php

namespace MysqlUuid\Formats;

/**
 * The traditional UUID string format
 */
class String extends Reorderable
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
        if ($this->getVariant() == 1 && $this->getVersion() == 4) {
            $parts = ['time_low', 'time_mid', 'time_high', 'clock_seq', 'node'];
        } else {
            $parts = ['node_high', 'node_low', 'time_high', 'clock_seq', 'time_midlow'];
        }

        $fields = array_combine($parts, explode('-', $value));

        if (!isset($fields['node'])) {
            $fields['node'] = $fields['node_high'] . $fields['node_low'];
        }

        if (!isset($fields['time_low']) || !isset($fields['time_mid'])) {
            $fields['time_mid'] = substr($fields['time_midlow'], 0, 4);
            $fields['time_low'] = substr($fields['time_midlow'], 3, 8);
        }

        return $fields;
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

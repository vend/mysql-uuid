<?php

namespace MysqlUuid\Formats;

/**
 * A 'standard' UUID that has had the node and
 */
class ReorderedString extends String
{
    /**
     * @inheritDoc
     */
    public function toFields($value)
    {
        $fields = parent::toFields($value);

        // Node identifier is split into time_low and time_mid
        $node = $fields['time_low'] . $fields['time_mid'];

        // Time mid and low are multiplexed into node
        $time_mid = substr($fields['node'], 0, 4);
        $time_low = substr($fields['node'], 4, 8);

        $fields['node']     = $node;
        $fields['time_low'] = $time_low;
        $fields['time_mid'] = $time_mid;

        return $fields;
    }

    /**
     * @inheritDoc
     */
    public function fromFields(array $fields)
    {
        $node_high = substr($fields['node'], 0, 8);
        $node_low  = substr($fields['node'], 8, 4);

        $time_midlow = $fields['time_mid'] . $fields['time_low'];

        return sprintf(
            '%s-%s-%s-%s-%s',
            $node_high,
            $node_low,
            $fields['time_high'],
            $fields['clock_seq'],
            $time_midlow
        );
    }
}

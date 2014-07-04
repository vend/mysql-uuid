<?php

namespace MysqlUuid\Formats;

class ReorderedString extends String
{
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
}

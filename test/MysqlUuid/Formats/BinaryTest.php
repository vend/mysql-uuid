<?php

namespace MysqlUuid\Formats;

class BinaryTest extends FormatTest
{
    /**
     * @return Format
     */
    protected function getSut()
    {
        return new Binary();
    }

    protected function good()
    {
        return [
            openssl_random_pseudo_bytes(16),
            str_repeat("\x00", 16),
            str_repeat("\xff", 16),
            str_repeat("\x80", 16)
        ];
    }

    protected function bad()
    {
        return [
            'e856c9f6-0306-11e4-9583-080027f3add4',
            'e856c9f6030611e49583080027f3add4',
            str_repeat("\x00", 15),
            str_repeat("\x00", 17)
        ];
    }

    /**
     * @return array<string,array>
     */
    protected function fields()
    {
        return [
            str_repeat("\x00", 16) => [
                'node'      => str_repeat('0', 12),
                'clock_seq' => str_repeat('0', 4),
                'time_high' => str_repeat('0', 4),
                'time_mid'  => str_repeat('0', 4),
                'time_low'  => str_repeat('0', 8)
            ],
            "\x40\x40\x78\x2F\xDE\x00\xB1\xF5\x11\xE2\x28\x71\x3F\x6F\x9E\xFC" => [
                'node'      => '4040782fde00',
                'clock_seq' => 'b1f5',
                'time_high' => '11e2',
                'time_mid'  => '2871',
                'time_low'  => '3f6f9efc'
            ]
        ];
    }
}

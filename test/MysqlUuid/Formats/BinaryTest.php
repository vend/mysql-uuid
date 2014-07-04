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
            "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00",
            "\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff\xff",
            openssl_random_pseudo_bytes(16)
        ];
    }

    protected function bad()
    {
        return [
            'e856c9f6-0306-11e4-9583-080027f3add4',
            'e856c9f6030611e49583080027f3add4',
            "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
        ];
    }
}

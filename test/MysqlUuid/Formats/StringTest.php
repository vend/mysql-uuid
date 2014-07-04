<?php

namespace MysqlUuid\Formats;

class StringTest extends FormatTest
{
    /**
     * @return Format
     */
    protected function getSut()
    {
        return new String();
    }

    protected function good()
    {
        return [
            'e856c9f6-0306-11e4-9583-080027f3add4',
            '00000000-0000-0000-0000-000000000000',
            'ffffffff-ffff-ffff-ffff-ffffffffffff',
            'deadbeef-cafe-babe-feed-beefdeadfeed'
        ];
    }

    protected function bad()
    {
        return [
            'e856c9f6-0306-11e4-9583-080027f3add',
            '856c9f6-0306-11e4-9583-080027f3add4'
        ];
    }
}

<?php

namespace MysqlUuid\Formats;

class HexTest extends FormatTest
{
    /**
     * @return Format
     */
    protected function getSut()
    {
        return new Hex();
    }

    protected function good()
    {
        return [
            'e856c9f6030611e49583080027f3add4',
            '00000000000000000000000000000000',
            'ffffffffffffffffffffffffffffffff',
            'deadbeefcafebabefeedbeefdeadfeed'
        ];
    }

    protected function bad()
    {
        return [
            'e856c9f6030611e49583080027f3add',
            '856c9f6030611e49583080027f3add4'
        ];
    }

    /**
     * @return array<string,array>
     */
    protected function fields()
    {
        return [];
    }
}

<?php

namespace MysqlUuid;

use MysqlUuid\Formats\Binary;
use MysqlUuid\Formats\Format;
use MysqlUuid\Formats\Hex;
use MysqlUuid\Formats\ReorderedString;
use MysqlUuid\Formats\String;
use MysqlUuid\Test\BaseTest;

class ReorderTest extends BaseTest
{
    /**
     * Test conversions between formats
     */
    public function testConvert()
    {
        foreach ($this->conversions() as $conversion) {
            $from = $this->getFormat($conversion['from']);
            $to   = $this->getFormat($conversion['to']);

            foreach ($conversion['cases'] as $before => $after) {
                $uuid      = new Uuid($before, $from);
                $converted = $uuid->toFormat($to);

                $this->assertEquals(
                    bin2hex($after),
                    bin2hex($converted),
                    sprintf('Converting %s from %s to %s', $before, $conversion['from'], $conversion['to'])
                );
            }
        }
    }

    /**
     * Gets an instance of the named format
     *
     * @param string $format
     * @return Format
     */
    protected function getFormat($format)
    {
        switch ($format) {
            case 'string':
                return new String();
            case 'reordered':
                return new ReorderedString();
            case 'binary':
                return new Binary();
            case 'hex':
                return new Hex();
        }
    }

    protected function conversions()
    {
        return [
            [
                'from'  => 'string',
                'to'    => 'reordered',
                'cases' => [
                    'b8e2adff-0331-11e4-9583-080027f3add4' => '080027f3-add4-11e4-9583-0331b8e2adff',
                    'b8fc7a3e-0331-11e4-9583-080027f3add4' => '080027f3-add4-11e4-9583-0331b8fc7a3e'
                ],
            ],
            [
                'from'  => 'string',
                'to'    => 'hex',
                'cases' => [
                    'b8e2adff-0331-11e4-9583-080027f3add4' => 'b8e2adff033111e49583080027f3add4',
                    'b8fc7a3e-0331-11e4-9583-080027f3add4' => 'b8fc7a3e033111e49583080027f3add4'
                ],
            ],
            [
                'from'  => 'string',
                'to'    => 'binary',
                'cases' => [
                    'b8e2adff-0331-11e4-9583-080027f3add4' => "\x08\x00\x27\xf3\xad\xd4\x95\x83\x11\xe4\x03\x31\xb8\xe2\xad\xff",
                    'b8fc7a3e-0331-11e4-9583-080027f3add4' => "\x08\x00\x27\xf3\xad\xd4\x95\x83\x11\xe4\x03\x31\xb8\xfc\x7a\x3e",
                    '00000000-0000-0000-0000-000000000000' => "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00"
                ],
            ],
        ];
    }
}

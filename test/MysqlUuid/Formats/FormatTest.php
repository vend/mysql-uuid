<?php

namespace MysqlUuid\Formats;

use MysqlUuid\Test\BaseTest;

abstract class FormatTest extends BaseTest
{
    /**
     * @return array<string>
     */
    abstract protected function good();

    /**
     * @return array<string>
     */
    abstract protected function bad();

    /**
     * @return array<string,array>
     */
    abstract protected function fields();

    /**
     * @return Format
     */
    abstract protected function getSut();

    /**
     * Tests the isValid method
     */
    public function testIsValid()
    {
        $format = $this->getSut();

        foreach ($this->good() as $good) {
            $this->assertTrue($format->isValid($good));
        }

        foreach ($this->bad() as $bad) {
            $this->assertFalse($format->isValid($bad));
        }
    }

    /**
     * Tests the toFields method
     */
    public function testToFields()
    {
        foreach ($this->fields() as $value => $fields) {
            $this->assertEquals($fields, $this->getSut()->toFields($value));
        }
    }
}

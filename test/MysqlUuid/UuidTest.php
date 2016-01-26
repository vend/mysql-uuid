<?php

namespace MysqlUuid;

use MysqlUuid\Formats\PlainString;
use MysqlUuid\Test\BaseTest;

class UuidTest extends BaseTest
{
    public function testNoInitialFormat()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-9583-080027f3add4');
        $this->assertTrue($uuid->isValid());

        $uuid = new Uuid('b8fc7a3e033111e49583080027f3add4');
        $this->assertFalse($uuid->isValid());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testBadFormat()
    {
        $format = $this->getMockBuilder('MysqlUuid\\Formats\\Format')
            ->getMockForAbstractClass();

        $format->expects($this->once())
            ->method('toFields')
            ->with('b8fc7a3e-0331-11e4-9583-080027f3add4')
            ->will($this->returnValue(null));

        $uuid = new Uuid('b8fc7a3e-0331-11e4-9583-080027f3add4', $format);
        $uuid->toFormat(new PlainString());
    }

    public function testFieldCrud()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-9583-080027f3add4');

        $this->assertTrue($uuid->isValid());
        $this->assertEquals('9583', $uuid->getField('clock_seq'));

        $uuid->setField('clock_seq', 'ffff');

        $this->assertEquals($uuid->toFormat(new PlainString()), 'b8fc7a3e-0331-11e4-ffff-080027f3add4');
    }
}

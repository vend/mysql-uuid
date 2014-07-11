<?php

namespace MysqlUuid;

use MysqlUuid\Test\BaseTest;
use ReflectionObject;

class VariantTest extends BaseTest
{
    protected $identify = [
        Variant::NCS => [
            'd0e817e1-e4b1-1801-3fe6-b4b60ccecf9d',
        ],

        Variant::RFC4122 => [
            'b8fc7a3e-0331-11e4-9583-080027f3add4',
            'd0e817e1-e4b1-1801-bfe6-b4b60ccecf9d',
        ],
        Variant::MICROSOFT => [
            'd0e817e1-e4b1-1801-dfe6-b4b60ccecf9d',
        ],
        Variant::FUTURE => [
            'd0e817e1-e4b1-1801-ffe6-b4b60ccecf9d'
        ]
    ];

    public function testIdentify()
    {
        foreach ($this->identify as $variant => $cases) {
            foreach ($cases as $case) {
                $uuid = new Uuid($case);
                $sut  = new Variant($uuid);

                $this->assertEquals($variant, $sut->get());
            }
        }
    }

    public function testIdentifyOther()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-ffff-080027f3add4');

        $variant = new Variant($uuid);

        // Clear out masks
        $mask = (new ReflectionObject($variant))->getProperty('mask');
        $mask->setAccessible(true);
        $mask->setValue($variant, []);

        $this->assertEquals(Variant::OTHER, $variant->get());
    }

    public function testSet()
    {
        foreach (Variant::$variants as $variant => $name) {
            $var = new Variant(new Uuid('b8fc7a3e-0331-11e4-0000-080027f3add4'));
            $var->set($variant);
            $this->assertEquals($variant, $var->get(), 'Changed UUID to ' . $name . ' variant');

            $var = new Variant(new Uuid('b8fc7a3e-0331-11e4-ffff-080027f3add4'));
            $var->set($variant);
            $this->assertEquals($variant, $var->get(), 'Changed UUID to ' . $name . ' variant');
        }
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetInvalid()
    {
        $var = new Variant(new Uuid('b8fc7a3e-0331-11e4-0000-080027f3add4'));
        $var->set('some rubbish');
    }
}

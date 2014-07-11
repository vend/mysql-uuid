<?php

namespace MysqlUuid;

use MysqlUuid\Formats\String;
use MysqlUuid\Test\BaseTest;
use ReflectionObject;

class VariantTest extends BaseTest
{
    public function testIdentifyRfc4122()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-9583-080027f3add4');
        $variant = new Variant($uuid);
        $this->assertEquals(Variant::RFC4122, $variant->get());
    }

    public function testIdentifyNcs()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-5583-080027f3add4');
        $variant = new Variant($uuid);
        $this->assertEquals(Variant::NCS, $variant->get());
    }

    public function testIdentifyMicrosoft()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-c583-080027f3add4');
        $variant = new Variant($uuid);
        $this->assertEquals(Variant::MICROSOFT, $variant->get());
    }

    public function testIdentifyFuture()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-e583-080027f3add4');
        $variant = new Variant($uuid);
        $this->assertEquals(Variant::FUTURE, $variant->get());
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

    public function testChangeVariant()
    {
        $uuid = new Uuid('b8fc7a3e-0331-11e4-e583-080027f3add4');

        foreach (Variant::$variants as $variant => $name) {
            $var = new Variant($uuid);
            $var->set($variant);

            var_dump($variant);
            var_dump($uuid->toFormat(new String()));

            $this->assertEquals($variant, $var->get(), 'Changed UUID to ' . $name . ' variant');
        }
    }
}

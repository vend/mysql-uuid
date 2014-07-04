<?php

namespace MysqlUuid;

use MysqlUuid\Formats\Binary;
use MysqlUuid\Formats\String;
use MysqlUuid\Test\BaseTest;

class ReorderTest extends BaseTest
{
    public function testUuidToBinary()
    {

        $uuid = 'e856c9f6-0306-11e4-9583-080027f3add4';

        $m = new MysqlUuid($uuid, new String());
        $binary = $m->toFormat(new Binary());


        var_dump($binary);


    }




}

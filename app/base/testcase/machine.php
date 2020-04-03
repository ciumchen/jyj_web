<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class machine extends PHPUnit_Framework_TestCase
{
    public function setUp(){
    }


    public function testCheck(){
        $res =  kernel::single('base_license_machine_check')->check();

        var_dump($res);
    }

}

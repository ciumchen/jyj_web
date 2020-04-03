<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class cleanRels extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testClean()
    {
        kernel::single('systag_rels')->cleanAllRels();
        return true;
    }
}



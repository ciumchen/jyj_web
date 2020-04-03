<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class license extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

//  public function testInfo()
//  {

//      $res = kernel::single('base_license_active')->getActiveInfo();
//      var_dump($res);
//  }

//  public function testActive()
//  {
//      $activeKey = 'T12345678';
//      $res = kernel::single('base_license_active')->active($activeKey);
//      var_dump($res);
//  }

    public function testCheck()
    {
        $res = kernel::single('base_license_active')->checkActiveKey();
        var_dump($res);
    }


}

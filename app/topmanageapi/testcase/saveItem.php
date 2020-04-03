<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class saveItem extends PHPUnit_Framework_TestCase
{
    public function setUp(){
        $this->url = 'http://union.mac.onex.software/shop/topapi';
        $nature_props = '["3":"25","6":"38"]';
        $images = array(
            '1_1' => 'http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg',
            );
        $spec_value = array(
            '1_1' => '白色',
            '1_2' => '蓝色',
            '2_3' => 'xl',
            );
        $this->apiParams = [
            'shop_id'       => '3',
            'cat_id'        => '33',
            'brand_id'      => '73',
            'shop_cat_id'   => '22',
            'title'         => '商品标题',
            'sub_title'     => '商品副标题',
            'weight'        => '1',
            'unit'          => 'kg',
            'list_image'    => '/images/6d/23/9f/2e58a2438b27365a5562642461c96258ba6551cc.jpg,http://images.bbc.shopex123.com/images/85/e0/07/4f2ccf44cf36f273bdd741bc3ee604b8d3488338.png,http://images.bbc.shopex123.com/images/2b/96/d4/27cff94f6f670d10ea9013d7a3ac9e29b94be170.png',
            'order_sort'    => '0',
            'approve_status'=> 'instock',
            'sub_stock'     => '0',
            'store'         => ' ',
            'price'         => '1',
            'cost_price'    => '1',
            'mkt_price'     => '1',
            'show_mkt_price'=> '1',
            'bn'            => '',
            'outer_id'      => '',
            'barcode'       => '',
            'use_platform'  => '0',
            'dlytmpl_id'    => '7',
            'desc'          => '\tpc端文描',
            'wap_desc'      => '\twap端文描',
            'nature_props'  => $nature_props,
            'nospec'        => '1',
            'images'        => $images,
            'spec'          => '{\"1\":{\"spec_name\":\"颜色\",\"spec_id\":\"1\",\"show_type\":\"image\",\"option\":{\"1\":{\"spec_value\":\"白色\",\"spec_value_id\":\"1\",\"spec_image\":\"http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg\",\"spec_image_url\":\"http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg_t.jpg\"},\"3\":{\"spec_value\":\"粉红色\",\"spec_value_id\":\"3\",\"spec_image\":\"http://images.bbc.shopex123.com/images/4d/6f/0b/94140ddf9790801f4cfb28d8006b64a479dbe9b5.jpg\",\"spec_image_url\":\"http://images.bbc.shopex123.com/images/4d/6f/0b/94140ddf9790801f4cfb28d8006b64a479dbe9b5.jpg_t.jpg\"},\"5\":{\"spec_value\":\"红色\",\"spec_value_id\":\"5\",\"spec_image\":\"http://images.bbc.shopex123.com/images/bd/18/9a/b3b104062408283e9a714c2e4fa4ce2e00c69721.jpg\",\"spec_image_url\":\"http://images.bbc.shopex123.com/images/bd/18/9a/b3b104062408283e9a714c2e4fa4ce2e00c69721.jpg_t.jpg\"}}},\"2\":{\"spec_name\":\"尺码\",\"spec_id\":\"2\",\"show_type\":\"text\",\"option\":{\"19\":{\"spec_value\":\"s\",\"spec_value_id\":\"19\"},\"21\":{\"spec_value\":\"l\",\"spec_value_id\":\"21\"}}}}',
            'spec_value'    => $spec_value,
            'params'        => '',
            'itemParams'    => '',
            'sku'           => '{\"f475876af4\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"白色\",\"2\":\"s\"},\"spec_value_id\":{\"1\":\"1\",\"2\":\"19\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-1\",\"barcode\":\"sn1111111-1\"},\"a4738c95e7\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"白色\",\"2\":\"l\"},\"spec_value_id\":{\"1\":\"1\",\"2\":\"21\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-2\",\"barcode\":\"sn1111111-2\"},\"dbd9e4fb05\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"粉红色\",\"2\":\"s\"},\"spec_value_id\":{\"1\":\"3\",\"2\":\"19\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-3\",\"barcode\":\"sn1111111-3\"},\"302805248e\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"粉红色\",\"2\":\"l\"},\"spec_value_id\":{\"1\":\"3\",\"2\":\"21\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-4\",\"barcode\":\"sn1111111-4\"},\"0d4c2e233e\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"红色\",\"2\":\"s\"},\"spec_value_id\":{\"1\":\"5\",\"2\":\"19\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-5\",\"barcode\":\"sn1111111-5\"},\"e7a265480f\":{\"sku_id\":\"new\",\"spec_desc\":{\"spec_value\":{\"1\":\"红色\",\"2\":\"l\"},\"spec_value_id\":{\"1\":\"5\",\"2\":\"21\"}},\"price\":\"100\",\"mkt_price\":\"101\",\"cost_price\":\"102\",\"store\":\"100\",\"bn\":\"bn1111111-6\",\"barcode\":\"sn1111111-6\"}}',
        ];
    }

    public function testRequest()
    {
        $url = $this->url;
        $apiParams = $this->apiParams;
        $result = client::post($url, ['form_params' => $apiParams])->getBody();
        var_dump($result);
    }
}

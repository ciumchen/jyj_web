<?php
/**
 * topapi
 *
 * -- item.create
 * -- 商品列表
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_create implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '添加、编辑商品';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
//          'shop_id' => ['type'=>'integer','valid'=>'required|min:1','description'=>'店铺id'],
            'item_id' => ['type'=>'integer','valid'=>'','description'=>'商品id，新增商品则不填'],//编辑接口需要
            'cat_id'  => ['type'=>'integer','valid'=>'required|integer|min:1','description'=>'商品类目ID','msg'=>'商品分类不能为空'],
            'brand_id' => ['type'=>'integer','valid'=>'required|integer|min:1','description'=>'品牌ID','msg'=>'品牌不能为空'],
            'shop_cat_id' => ['type'=>'string','valid'=>'required','description'=>'商家自定义分类id'],

            'title' => ['type'=>'string','valid'=>'required|string|max:50','description'=>'商品标题'],
            'sub_title' => ['type'=>'string','valid'=>'string|max:150','description'=>'商品子标题、卖点，请不要超过150个字符,不要有特殊字符'],
            'weight' => ['type'=>'numeric','valid'=>'required|numeric|min:0.01','description'=>'商品重量,单位kg', 'msg'=>'重量必填|重量必须为数字|重量必须大于0'],
            'unit' => ['type'=>'string','valid'=>'','description'=>'计价单位'],
            'list_image' => ['type'=>'string','valid'=>'','description'=>'商品图片，支持多个，用逗号分隔,','example'=>'images/29/e5/22/670cf312b0aaace1ebf6305d6f346ee147f29c16.jpg,images/65/8e/c4/bbeb17e97aad90ff3d777bf45e84bfce754f6611.jpg'],

//          'approve_status' => ['type'=>'string','valid'=>'in:onsale,instock','description'=>'商品状态（上下架）'],
            'sub_stock' => ['type'=>'boolean','valid'=>'in:0,1','description'=>'减库存方式，0:付款减库存,1:下单减库存'],

//          'item_id' => ['type'=>'integer','valid'=>'','description'=>'商品id，新增商品则不填'],//编辑接口需要
            'store' => ['type'=>'string','valid'=>'','description'=>'商品级别的库存'],
            'price' => ['type'=>'numeric','valid'=>'required|numeric|min:0.01|max:10000000000', 'description'=>'标准售价', 'msg'=>'标准售价必填|标准售价必须是正整数|标准售价必须大于0|标准售价最大10000000000'],
            'cost_price' => ['type'=>'numeric','valid'=>'min:0.01', 'description'=>'成本价', 'msg'=>'成本价必须大于0的数字|成本价必须大于0的数字'],
            'mkt_price' => ['type'=>'numeric','valid'=>'','description'=>'原价'],
            'show_mkt_price' => ['type'=>'boolean','valid'=>'boolean','description'=>'是否在商品页展示原价'],
            'bn' => ['type'=>'string','valid'=>'','description'=>'商品编号，不填会自动生成'],
            'outer_id' => ['type'=>'string','valid'=>'max:30','description'=>'商家外部编码'],
            'barcode' => ['type'=>'string','valid'=>'','description'=>'商品级别的条形码'],

            'use_platform' => ['type'=>'string','valid'=>'in:0,1,2','description'=>'使用平台,0->全部，1->PC，2->移动'],
            'dlytmpl_id' => ['type'=>'integer','valid'=>'required|integer|min:1','description'=>'运费模板ID','msg'=>'运费模板不能为空'],
            'desc' => ['type'=>'string','valid'=>'','description'=>'pc端文描'],
            'wap_desc' => ['type'=>'string','valid'=>'','description'=>'wap端文描'],

            'nature_props' => ['type'=>'array','valid'=>'','description'=>'自然属性信息'],
            'nospec' => ['type'=>'boolean','valid'=>'required|in:0,1','description'=>'是否为多规格商品：0表示多规格，1表示单规格商品'],
            'images' => ['type'=>'array','valid'=>'','description'=>'颜色属性图片'],
            'spec' => ['type'=>'string','valid'=>'','description'=>'选择的规格信息(json格式)'],
            'spec_value' => ['type'=>'array','valid'=>'','description'=>'选择的规格值信息'],
            'params' => ['type'=>'string','valid'=>'','description'=>'商品参数序列化'],
            'itemParams' => ['type'=>'string','valid'=>'','description'=>'商品参数序列化'],
            'sku' => ['type'=>'jsonArray', 'valid'=>'required_if:nospec,0', 'example'=>'', 'description'=>'sku信息' ,'params' => [
                'price'      => ['type'=>'numeric',  'valid'=>'numeric|min:0.01','description'=>'SKU销售价', 'msg'=>'SKU销售价必填|SKU销售价必须是正整数|SKU销售价必须大于0|SKU销售价最大10000000000'],
                'mkt_price'  => ['type'=>'numeric',  'valid'=>'min:0', 'description'=>'SKU原价'],
                'cost_price'   => ['type'=>'numeric',  'valid'=>'min:0', 'description'=>'SKU成本价'],
                'store' => ['type'=>'integer',  'valid'=>'required|numeric|min:1|max:999999', 'description'=>'SKU库存', 'msg'=>'SKU库存必填|SKU库存必须是正整数|SKU库存必须大于0|SKU库存最大999999'],
                'bn' => ['type'=>'string',  'valid'=>'', 'description'=>'SKU编号，不填自动生成'],
                'barcode' => ['type'=>'string',  'valid'=>'', 'description'=>'SKU条形码'],
                'spec_desc' => ['type'=>'string',  'valid'=>'', 'description'=>'销售属性值'],
                'spec_info' => ['type'=>'string',  'valid'=>'', 'description'=>'销售属性文字描述'],
                'outer_id' => ['type'=>'string',  'valid'=>'', 'description'=>'外部编码'],
                'sku_id' => ['type'=>'integer',  'valid'=>'', 'description'=>'商品sku_id，新增则不填'],//新增产品不需要此字段
            ], 'msg'=>'多规格商品，请选择销售属性'],

        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $this -> _checkParams($params);
        $requestParams = [];
        if($params['item_id']){
            $requestParams['item_id']       = $params['item_id'];//商品id，标识这个商品是新增还是修改
        }
        $requestParams['shop_id']           = $params['oAuthInfo']['shop_id'];//商家id
        $requestParams['cat_id']            = $params['cat_id'];//分类id
        $requestParams['selected_catname']  = $params['selected_catname'];
        $requestParams['brand_id']          = $params['brand_id'];//品牌id
        $requestParams['shop_cat_id']       = $this->genShopCatId($params['shop_cat_id']);//店铺分类id
        $requestParams['title']             = htmlspecialchars($params['title']);//标题
        $requestParams['sub_title']         = htmlspecialchars($params['sub_title']);//副标题
        $requestParams['weight']            = $params['weight'];//重量，单位kg
        $requestParams['unit']              = $params['unit'];//计量单位
        $requestParams['list_image']        = $params['list_image'];//商品图片列表
        $requestParams['order_sort']        = 0;
        $requestParams['approve_status']    = 'instock';//商品状态是下架
        $requestParams['sub_stock']         = $params['sub_stock'];//这个是扣减库存，0付款减库存，1是下单减库存
        $requestParams['store']             = $params['store'];//商品级别库存，所有sku库存之和
        $requestParams['price']             = $params['price'];//价格
        $requestParams['cost_price']        = $params['cost_price'];//成本价，就是看看而已
        $requestParams['mkt_price']         = $params['mkt_price'];//市场价，就是看看而已
        $requestParams['show_mkt_price']    = $params['show_mkt_price'];//前台是否展示市场价
        $requestParams['bn']                = $params['bn'];//编号
        $requestParams['outer_id']          = $params['outer_id'];//商家自己的id，这个东西就是记录一下，连展示都没有
        $requestParams['barcode']           = $params['barcode'];//条形码
        $requestParams['use_platform']      = $params['use_platform'];//支持平台，0全平台，1PC，2WAP
        $requestParams['dlytmpl_id']        = $params['dlytmpl_id'];//运费模板id
        $requestParams['desc']              = $this->__removeScript($params['desc']);//PC端详情
        $requestParams['wap_desc']          = $this->__removeScript($params['wap_desc']);//wap端详情
        $requestParams['nature_props']      = $this->genNatureProps($params['nature_props']);//商品自然属性
        $requestParams['nospec']            = $params['nospec'];//0多规格商品，1单规格商品
        //$requestParams['images']            = $this->genSpecImages($params['images']);//规格图片 app端没有 
        $requestParams['spec']              = $params['spec'];//规格详情
        $requestParams['spec_value']        = $params['spec_value'];//规格值详情(所有的规格值都要传)
        $requestParams['params']            = $this->genParams($params['params']);//规格值详情(所有的规格值都要传)
        $requestParams['itemParams']        = $this->genItemParams($params['itemParams']);//规格值详情(所有的规格值都要传)
        $requestParams['sku']               = $this->genSku($params['sku']);//规格值详情(所有的规格值都要传)
        $result = app::get('topshop')->rpcCall('item.create',$requestParams);
        if($result)
        {
            $msg = app::get('topshop')->_('商品保存成功！');
            return array('status' => 'success', 'message' => $msg );
        }else{
            $msg = app::get('topshop')->_('商品保存失败,未知原因');
            return array('status' => 'error', 'message' => $msg );
        }
        
    }

    /*
     * @param string sku sku详情的json
     * @return string sku详情的json
     *
     */
    public function genSku($sku)
    {
        $sku = json_encode($sku);
        /*$sku = '{"f475876af4":{"sku_id":"new","spec_desc":{"spec_value":{"1":"白色","2":"s"},"spec_value_id":{"1":"1","2":"19"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-1","barcode":"sn1111111-1"},"a4738c95e7":{"sku_id":"new","spec_desc":{"spec_value":{"1":"白色","2":"l"},"spec_value_id":{"1":"1","2":"21"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-2","barcode":"sn1111111-2"},"dbd9e4fb05":{"sku_id":"new","spec_desc":{"spec_value":{"1":"粉红色","2":"s"},"spec_value_id":{"1":"3","2":"19"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-3","barcode":"sn1111111-3"},"302805248e":{"sku_id":"new","spec_desc":{"spec_value":{"1":"粉红色","2":"l"},"spec_value_id":{"1":"3","2":"21"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-4","barcode":"sn1111111-4"},"0d4c2e233e":{"sku_id":"new","spec_desc":{"spec_value":{"1":"红色","2":"s"},"spec_value_id":{"1":"5","2":"19"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-5","barcode":"sn1111111-5"},"e7a265480f":{"sku_id":"new","spec_desc":{"spec_value":{"1":"红色","2":"l"},"spec_value_id":{"1":"5","2":"21"}},"price":"100","mkt_price":"101","cost_price":"102","store":"100","bn":"bn1111111-6","barcode":"sn1111111-6"}}';*/
        /**
         * json_decode后的结果
         * $key 随机字符串,不重复即可。以数组方式传入也可
         *  [
         *      $key => [
         *          'sku_id' => 'new',//新增为new，修改为原始sku_id
         *          'spec_desc' => [
         *              'spec_value' => [
         *                  $spec_id => $spec_value_name,
         *                  $spec_id => $spec_value_name,
         *              ],
         *              'spec_value_id' => [
         *                  $spec_id => $spec_value_id,
         *                  $spec_id => $spec_value_id,
         *              ],
         *          ],
         *          'price' => $price,//该sku的售价
         *          'mkt_price' => $mkt_price,//该sku的市场价
         *          'cost_price' => $cost_price,//该sku的成本价
         *          'store' => $store,//该sku的库存
         *          'bn' => $bn,//该sku的商品编号，可以理解为商家定义的商品唯一标识符
         *          'barcode' => $barcode,//该sku的条形码，不具备任何逻辑意义，只是记录一下
         *      ],
         *  ]
         */

        return $sku;
    }
    /**
     * 商品参数序列化
     * [genItemParams description]
     * @AuthorHTL
     * @DateTime  2017-08-30T16:31:14+0800
     * @param     [type]                   $itemParams [description]
     * @return    [type]                               [description]
     */
    public function genItemParams($itemParams)
    {

        /**
         * $key 为自定义不重复唯一标识符，与genParams()一致即可
         * [
         *     'group' => [
         *         $key => $param_group_name,
         *         $key => $param_group_name,
         *         $key => $param_group_name,
         *     ],
         *     'item' => [
         *         $key => $param_name,
         *         $key => $param_name,
         *         $key => $param_name,
         *     ],
         * ]
         */

        return $itemParams;
    }

    /**
     * 商品参数序列化
     * [genParams description]
     * @AuthorHTL
     * @DateTime  2017-08-30T16:31:53+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function genParams($params)
    {
        /**
         *    $key 为自定义不重复唯一标识符，与genItemParams()一致即可
         *  [
         *      $key => $params_name,
         *      $key => $params_name,
         *      $key => $params_name,
         *  ]
         **/
        return $params;
    }

    //选择的规格值信息
    public function genSpecValue($spec_value)
    {
        //这里的规格值是要所有的，name可以自定义
        /**
         *  [
         *      $spec_id . '_' . $spec_value_id => $spec_value_name,
         *      $spec_id . '_' . $spec_value_id => $spec_value_name,
         *      $spec_id . '_' . $spec_value_id => $spec_value_name,
         *  ]
         *
         *
         **/
        return $spec_value;
    }

    //自然属性信息
    public function genNatureProps($nature_props)
    {
        $aa = str_replace('[','{',$nature_props);
        $bb = str_replace(']','}',$aa);
        $nature_props = json_decode($bb,1);
        /*$nature_props = array(
            '3' => '25',
            '6' => '38',
            );*/
        /**
         * [
         *     $nature_props_id=>$nature_props_value_id,
         *     $nature_props_id=>$nature_props_value_id,
         *     $nature_props_id=>$nature_props_value_id,
         * ];
         *
         **/
        return $nature_props;
    }

//颜色属性图片
    public function genSpecImages($images)
    {

        /*$images = array(
            '1_1' => 'http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg',
            );*/
        /**
         * [
         *     $spec_id . '_' . $spec_value_id => $image,
         *     $spec_id . '_' . $spec_value_id => $image,
         *     $spec_id . '_' . $spec_value_id => $image,
         * ];
         */
        return $images;
    }

//选择的规格信息(json格式)
    public function genSpec($spec)
    {
        $spec = json_encode($spec);
        //
        /*$spec = '{"1":{"spec_name":"颜色","spec_id":"1","show_type":"image","option":{"1":{"spec_value":"白色","spec_value_id":"1","spec_image":"http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg","spec_image_url":"http://images.bbc.shopex123.com/images/b4/88/e5/1633d8c4f5b349e862f2def11a5cf29ade116e0b.jpg_t.jpg"},"3":{"spec_value":"粉红色","spec_value_id":"3","spec_image":"http://images.bbc.shopex123.com/images/4d/6f/0b/94140ddf9790801f4cfb28d8006b64a479dbe9b5.jpg","spec_image_url":"http://images.bbc.shopex123.com/images/4d/6f/0b/94140ddf9790801f4cfb28d8006b64a479dbe9b5.jpg_t.jpg"},"5":{"spec_value":"红色","spec_value_id":"5","spec_image":"http://images.bbc.shopex123.com/images/bd/18/9a/b3b104062408283e9a714c2e4fa4ce2e00c69721.jpg","spec_image_url":"http://images.bbc.shopex123.com/images/bd/18/9a/b3b104062408283e9a714c2e4fa4ce2e00c69721.jpg_t.jpg"}}},"2":{"spec_name":"尺码","spec_id":"2","show_type":"text","option":{"19":{"spec_value":"s","spec_value_id":"19"},"21":{"spec_value":"l","spec_value_id":"21"}}}}';*/
        /**
         *  [
         *      $spec_id => [
         *          'spec_name' => $spec_name,
         *          'spec_id' => $spec_id,
         *          'show_type' => 'image',//in_array($show_type, ['image','text'])
         *          'option' => [
         *              'spec_value_id'=>[
         *                  'spec_value' => $spec_value,
         *                  'spec_value_id' => $spec_value_id,
         *                  'spec_image' => $spec_image, //规格值原有的图片
         *                  'spec_image_url' => $spec_image_url, //自定义的规格值图片
         *              ],
         *          ],
         *      ],
         *      $spec_id => [
         *          'spec_name' => $spec_name,
         *          'spec_id' => $spec_id,
         *          'show_type' => 'text',//in_array($show_type, ['image','text'])
         *          'option' => [
         *              'spec_value_id'=>[
         *                  'spec_value' => $spec_value,
         *                  'spec_value_id' => $spec_value_id,
         *              ],
         *          ],
         *      ],
         *  ];
         *
         */

        return $spec;
    }

    public function genShopCatId($shop_cat_id)
    {
        //["shop_cids"]=> array(1) { [0]=> string(2) "22" }
        return $shop_cat_id;
    }

    private function _checkParams($params)
    {
        if(mb_strlen($params['title'],'UTF-8') > 50)
        {
            throw new Exception('商品名称至多50个字符');
        }
        if($params['spec_value'])
        {
            foreach($params['spec_value'] as $val)
            {
                if(mb_strlen($val,'UTF-8') > 20)
                {
                    throw new Exception('销售属性名称至多20个字符');
                }
            }
        }
    }


    protected function __removeScript($str)
    {
        $a ="script";
        $str1 = str_replace($a,' ',$str);
        return $str1;
    }

}

<?php
/**
 * topmanageapi
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_shop_writeSetting implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '保存店铺配置';

    /**
     * 哪些选项可以发送给前台
     */
    private $range = ['shop_descript', 'qq', 'wangwang', 'shop_logo'];

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'shop_descript'    => ['type'=>'string', 'valid'=>'max:200', 'example'=>'1', 'desc'=>'店铺简介，不要超过200个字', 'msg'=>''],
            'qq'               => ['type'=>'string', 'valid'=>'', 'example'=>'1', 'desc'=>'店铺qq客服，如果平台配置了在线客服，将使用该字段', 'msg'=>''],
            'wangwang'         => ['type'=>'wangwang', 'valid'=>'', 'example'=>'1', 'desc'=>'店铺旺旺客服，如果平台配置了在线客服，将使用该字段', 'msg'=>''],
            'shop_logo'        => ['type'=>'string', 'valid'=>'max:200', 'example'=>'1', 'desc'=>'店铺logo, 上传图片接口返回的图片id', 'msg'=>''],
        ];
    }

    /**
     * @return bool ret 是否保存成功
     */
    public function handle($params)
    {
        $config = $this->genConfig($params);

        $config['shop_id'] = $params['oAuthInfo']['shop_id'];
        $result = app::get('topshop')->rpcCall('shop.update',$config);
        return ['ret' => $result];
    }

    public function genConfig($params)
    {
        $config = [];
        foreach($this->range as $key)
        {
            if(isset($params[$key]))
                $config[$key] = $params[$key];
        }

        return $config;
    }

}



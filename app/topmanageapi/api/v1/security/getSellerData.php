<?php
/**
 * topapi
 *
 * -- security.getSellerData
 * -- 获取卖家信息
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_security_getSellerData implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取卖家信息';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
        ];
    }

    /**
     * [发送手机或邮箱验证码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [array]                           [卖家信息]
     */
    public function handle($params)
    {
        $seller_id = $params['oAuthInfo']['seller_id'];
        $sellData = shopAuth::getSellerData ($seller_id);
        return $sellData;
    }
}



<?php
/**
 * topmanageapi
 *
 * -- customer.consult.display
 * -- 咨询是否显示至商品详情
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_consultDisplay implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '咨询是否显示至商品详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'id'  => ['type'=>'int', 'valid'=>'required' , 'example'=>'1','desc'=>'回复的咨询id'],
            'status' => ['type'=>'bool','valid'=>'required', 'default'=>'true', 'example'=>'', 'description'=>'关闭显示 true 显示 false']
        ];
    }

    /**
     * @return array 咨询回复
     */
    public function handle($params)
    {
        $postParams['shop_id']      = $params['oAuthInfo']['shop_id'];
        $postParams['id']           = intval($params['id']);
        $postParams['status']       = $params['status'];

        $result = kernel::single('topmanageapi_customer_consult')->doDisplay($postParams);
        kernel::single('topmanageapi_logger')->addLog('商家app 更新咨询、回复显示状态。',$params);
        return $result;
    }

}
<?php
/**
 * topapi
 *
 * -- customer.aftersale.send
 * -- 审核换货填写物流信息
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_afterSaleSend implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '审核换货填写物流信息';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'aftersales_bn'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'1','desc'=>'售后编号'],
            'corp_code' => ['type'=>'int', 'valid'=>'', 'description'=>'物流公司代码'],
            'logi_name' => ['type'=>'string', 'valid'=>'', 'description'=>'物流公司名称'],
            'logi_no' => ['type'=>'string', 'valid'=>'required|max:20|min:6', 'description'=>'物流单号'],

        ];
    }

    /**
     * [handle 审核售后申请]
     * @param  [type] $params [description]
     * @return [type]         [description]
     */
    public function handle($params)
    {
        $postParams['shop_id'] =$params['oAuthInfo']['shop_id'];
        $postParams['aftersales_bn'] = trim($params['aftersales_bn']);
        if ($params['corp_code'] == 'other' && !$params['logi_name'])
        {
            return ['status'=>'error' ,'message'=>'其他物流公司不能为空'];
        }
        $postParams['corp_code'] = trim($params['corp_code']);
        $postParams['logi_name'] = $params['logi_name'];
        $postParams['logi_no'] = trim($params['logi_no']);
        $result = kernel::single('topmanageapi_customer_afterSales')->sendConfirm($postParams);
        $memo = '商家app售后操作。换货重新发货。申请售后的订单编号是'.$params['aftersales_bn'];
        kernel::single('topmanageapi_logger')->addLog($memo,$params);
        return $result;
    }
}
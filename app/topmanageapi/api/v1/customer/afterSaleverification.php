<?php
/**
 * topapi
 *
 * -- customer.aftersale.info
 * -- 审核售后申请
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_afterSaleverification implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '审核售后申请';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'aftersales_bn'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'1','desc'=>'售后编号'],
            'aftersales_type'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'','desc'=>'退换货类型'],
            'shop_explanation'  => ['type'=>'string', 'valid'=>'max:300' , 'example'=>'','desc'=>'商家处理申请说明'],
            'check_result'  => ['type'=>'bool', 'valid'=>'required' , 'example'=>'','desc'=>'审核状态'],
            //以下为申请仅退款需要传入的参数
            'total_price' => ['type'=>'money', 'valid'=>'','description'=>'退款金额'],
            'refunds_reason' => ['type'=>'string', 'valid'=>'|max:300','description'=>'退款申请原因|退款申请原因必须小于300字']

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
        $postParams['aftersales_type'] = trim($params['aftersales_type']);
        $postParams['shop_explanation'] = htmlspecialchars($params['shop_explanation']);
        $postParams['check_result'] = $params['check_result'];
        $result = kernel::single('topmanageapi_customer_afterSales')->verification($postParams);
        return $result;
    }
}
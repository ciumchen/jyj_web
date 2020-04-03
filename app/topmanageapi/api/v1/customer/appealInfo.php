<?php
/**
 * topmanageapi
 *
 * -- customer.appeal.info
 * -- 申诉详情
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_appealInfo implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '申诉详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'rate_id'  => ['type'=>'int', 'valid'=>'required' , 'example'=>'','desc'=>'申诉信息id'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段']
        ];
    }

    /**
     * @return array 申诉详情
     */
    public function handle($params)
    {
        $postParams['role'] = 'seller';
        $postParams['rate_id'] = intval($params['rate_id']);
        if (!isset($params['fields']))
        {
            $postParams['fields'] = 'rate_id,item_id,item_title,item_pic,item_price,result,content,rate_pic,created_time,is_appeal,appeal_status,appeal.appeal_id,appeal.content,appeal.appeal_type,appeal.evidence_pic,appeal.reject_reason,appeal.appeal_log';
        }else{
            $postParams['fields'] = $params['fields'];
        }
        $result = kernel::single('topmanageapi_customer_appeal')->getAppealInfo($postParams);
        return $result;
    }

}
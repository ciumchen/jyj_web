<?php
/**
 * topmanageapi
 *
 * -- customer.appeal.list
 * -- 申诉列表
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_appealList implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '申诉列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'appeal_again'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'all','desc'=>'申诉进度all 全部 0 一次申诉 1 二次申诉'],
            'appeal_status'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'WAIT,REJECT,SUCCESS,CLOSE','desc'=>'申诉结果REJECT:申诉驳回,WAIT:等待批准,SUCCESS:修改成功,CLOSE:申诉关闭'],
            'itme_title'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'商品标题'],
            'appeal_time'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'申诉时间'],
            'pages_no'   => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'每页数据条数,默认10条'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段']
        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $postParams['role'] = 'seller';
        $postParams['shop_id'] =$params['oAuthInfo']['shop_id'];
        $postParams['page_no'] = $params['pages_no'] ? $params['pages_no'] : 1;
        $postParams['page_size'] = $params['page_size'] ? $params['page_size'] : 10;
        $postParams['fields'] = $params['fields'] ? $params['fields'] : 'rate_id,tid,item_title,result,item_id,item_price,user_id,appeal_status,appeal_again,appeal_time,appeal.content,appeal.rate_id,appeal.evidence_pic';
        if ($params['item_title'])
        {
            $postParams['item_title'] = $params['item_title'];
        }
        $postParams['appeal_status'] = trim($params['appeal_status']);
        if (trim($params['appeal_again']) != 'all')
        {
            $postParams['appeal_again'] = $params['appeal_again'];
        }
        if ($params['appeal_time'])
        {
            $timeData = explode('-', $params['appeal_time']);
            $startTime = explode('/',trim($timeData[0]));
            $endTime = explode('/',trim($timeData[1]));
            $appealStartTime = mktime(0,0,0,$startTime[1],$startTime[2],$startTime[0]);
            $appealEndTime = mktime(24,60,60,$endTime[1],$endTime[2],$endTime[0]);
            $postParams['appeal_start_time'] = $appealStartTime;
            $postParams['appeal_end_time'] = $appealEndTime;
        }
        $result = kernel::single('topmanageapi_customer_appeal')->getAppealList($postParams);
        return $result;

    }

}
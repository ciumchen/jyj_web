<?php
/**
 * topmanageapi
 *
 * -- customer.comment.list
 * -- 评论列表
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_commentList implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '评论列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'result'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'all','desc'=>'评论分类'],
            'is_pic'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'有晒图'],
            'is_reply'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'有回复'],
            'content'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'有内容'],
            'itme_title'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'商品标题'],
            'rate_time'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'评价时间'],
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
        $postParams['shop_id'] =$params['oAuthInfo']['shop_id'];
        $postParams['page_no'] = $params['pages_no'] ? $params['pages_no'] : 1;
        $postParams['page_size'] = $params['page_size'] ? $params['page_size'] : 10;
        $postParams['fields'] = $params['fields'] ? $params['fields'] : '*,append';
        if ($params['is_pic'])
        {
            $postParams['is_pic'] = true;
        }
        if ($params['content'])
        {
            $postParams['is_content'] = true;
        }
        if ($params['is_reply'])
        {
            $postParams['is_reply'] = true;
        }
        if ($params['item_title'])
        {
            $postParams['item_title'] = $params['item_title'];
        }
        if ($params['rate_time'])
        {
            $timeData = explode('-', $params['rate_time']);
            $startTime = explode('/',trim($timeData[0]));
            $endTime = explode('/',trim($timeData[1]));
            $appealStartTime = mktime(0,0,0,$startTime[1],$startTime[2],$startTime[0]);
            $appealEndTime = mktime(24,60,60,$endTime[1],$endTime[2],$endTime[0]);
            $postParams['rate_start_time'] = $appealStartTime;
            $postParams['rate_end_time'] = $appealEndTime;
        }
        if( in_array($params['result'], ['good','bad', 'neutral']) )
        {
            $postParams['result'] = $params['result'];
        }

        $result = kernel::single('topmanageapi_customer_comment')->getCommentList($postParams);
        return $result;

    }

}
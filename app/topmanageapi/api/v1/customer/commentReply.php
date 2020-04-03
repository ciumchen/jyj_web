<?php
/**
 * topapi
 *
 * -- customer.comment.reply
 * -- 回复评价
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_commentReply implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '回复评价';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'rate_id'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'1','desc'=>'评价id'],
            'type'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'','desc'=>'初次评价为add'],
            'reply_content'  => ['type'=>'string', 'valid'=>'required|min:5|max:300' , 'example'=>'','desc'=>'商家处理申请说明','msg'=>'回复内容必填|回复内容最少5个字|回复内容最多300个字']

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
        $postParams['rate_id'] = intval($params['rate_id']);
        $postParams['type'] = trim($params['type']);
        $postParams['reply_content'] = htmlspecialchars($params['reply_content']);
        $result = kernel::single('topmanageapi_customer_comment')->reply($postParams);
        return $result;
    }
}
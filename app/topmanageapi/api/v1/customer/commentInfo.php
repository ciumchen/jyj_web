<?php
/**
 * topmanageapi
 *
 * -- customer.comment.info
 * -- 评论详情
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_commentInfo implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '评论详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'rate_id'  => ['type'=>'int', 'valid'=>'required' , 'example'=>'','desc'=>'评论id'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段']
        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $postParams['shop_id'] =$params['oAuthInfo']['shop_id'];
        //接口调用身份说明
        $postParams['role'] = 'seller';
        $postParams['rate_id'] = $params['rate_id'];
        $postParams['fields'] = $params['fields'] ? $params['fields'] : '*,append';

        $result = kernel::single('topmanageapi_customer_comment')->getCommentInfo($postParams);
        return $result;

    }

}
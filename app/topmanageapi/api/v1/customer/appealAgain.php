<?php
/**
 * topmanageapi
 *
 * -- customer.appeal.again
 * -- 评价申诉
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_appealAgain implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '评价申诉';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'rate_id'  => ['type'=>'int', 'valid'=>'required' , 'example'=>'','desc'=>'申诉信息id'],
            'is_again'  => ['type'=>'bool', 'valid'=>'required' , 'example'=>'','desc'=>'是否是二次申诉'],
            'appeael_type'  => ['type'=>'string', 'valid'=>'' , 'example'=>'APPLY_DELETE','desc'=>'二次申诉类型 APPLY_DELETE:删除该条评论 APPLY_UPDATE: 要求用户修改评价'],
            'appeal_content'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'','desc'=>'申诉说明'],
            'evidence_pic'  => ['type'=>'array', 'valid'=>'' , 'example'=>'','desc'=>'举证图片'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段']
        ];
    }

    /**
     * @return array 评价申诉
     */
    public function handle($params)
    {
        kernel::single('topmanageapi_logger')->addLog('商家app提交评价申诉。申诉的评价ID是 '.$params['rate_id'] ,$params);
        $shop_id = $params['oAuthInfo']['shop_id'];
        $postParams['rate_id'] = intval($params['rate_id']);
        $postParams['is_again'] = $params['is_again'] ? $params['is_again'] : 'false';
        $postParams['appeael_type'] = $params['appeael_type'];
        $postParams['content'] = $params['appeal_content'];
        if ($params['evidence_pic'])
        {
            $postParams['evidence_pic'] = implode(',',$params['evidence_pic']);
        }
        if ($params['is_again'] && !isset($params['evidence_pic']))
        {
            return ['status'=>'error','message'=>'再次申诉，图片凭证必填'];
        }
        $result = kernel::single('topmanageapi_customer_appeal')->appeal($postParams,$shop_id);

        return $result;

    }






}
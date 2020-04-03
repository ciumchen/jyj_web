<?php
/**
 * topapi
 *
 * -- index.countdata
 * -- app首页统计数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_index_countData implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = 'app首页UV,代付款数据';

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
     * @return array app首页统计数据
     */
    public function handle($params)
    {
        //获取店铺昨日UV
        $result = [];
        $shop_id = $params['oAuthInfo']['shop_id'];
        $date = date('Ymd', strtotime('-1 day'));
        //店铺昨日访问量
        $uv = redis::scene('traffic')->hget('webuv:all_'.$shop_id, $date);
        if(empty($uv))
        {
            $result['uv'] = 0;
        }else
        {
            $result['uv'] = $uv;
        }
        //代付款金额
        $paramData =[];
        $paramData['shop_id'] = $shop_id;
        $paramData['status'] = 'WAIT_BUYER_PAY';
        $result['countPayment'] =kernel::single('topmanageapi_getAverPrice')->tradeCount($paramData);
        return $result;
    }

}



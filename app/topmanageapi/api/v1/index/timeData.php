<?php
/**
 * topapi
 *
 * -- index.timeData
 * -- 昨日销售商品排行
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_index_timeData implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '每个时间点的销售数据';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
        'timeType' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'传入的时间类型 一共有5种(yesterday,beforday,week,month,today)']
        ];
    }

    /**
     * @return array 商品销售排行
     */
    public function handle($params)
    {
        //昨日销量排行第一
        $result = [];
        $timeType = $params['timeType'];
        $shop_id = $params['oAuthInfo']['shop_id'];
        $day = kernel::single('topmanageapi_getAverPrice')->get($timeType,$shop_id);
        if ($day['new_trade'] == 0) {
            $result['new_trade'] = 0;
            $result['averPrice'] = 0;
        }else{
            //昨日新增订单数
            $result['new_trade'] = $day['new_trade'];
            //昨日成交金额
            $result['averPrice'] = $day['averPrice'];
        }
        return $result;
    }

}



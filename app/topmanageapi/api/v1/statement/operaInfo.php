<?php
/**
 * topapi
 *
 * -- statement.shop.opera.info
 * -- 商家运营概况
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_operaInfo implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家运营概况';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>'']
        ];
    }

    /**
     * @return array sysstat 头部交易数据
     * @return string sysstat.commonday.new_fee 新增订单金额
     * @return string sysstat.beforeweek.new_fee 上周同期新增订单金额
     * @return string sysstat.commonday.new_trade 新增订单数
     * @return string sysstat.beforeweek.new_trade 上周同期新增订单数
     * @return string sysstat.commonday.averPrice 评价单价
     * @return string sysstat.beforeweek.averPrice 评价单价
     * @return array sysDataInfo.yesterday 昨日交易数据
     * @return array sysDataInfo.beforInfo 前日交易数据
     * @return array sysDataInfo.week 近7日交易数据
     * @return array sysDataInfo.month 近30日交易数据
     * @return array sysItemInfo 商品销售榜
     * @return array trafficData 独立访问量UV
     */
    public function handle($params)
    {
        $shopId = $params['oAuthInfo']['shop_id'];
        $type = $params['timetype'];
        $result = kernel::single('topmanageapi_statement_operaInfo')->get_operaInfo_data($type);
        return $result;
    }
}



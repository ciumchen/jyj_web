<?php
/**
 * topapi
 *
 * -- customer.aftersale.info
 * -- 退换货详情
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_afterSaleinfo implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '退换货详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'aftersales_bn'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'1','desc'=>'售后编号']
        ];
    }

    /**
     * @return array    info 订单详情
     * @return string   info.aftersales_bn 退换货编号
     * @return string   info.created_time 申请时间
     * @return string   info.progress 申请处理进度
     * @return string   info.aftersales_type 退换货类型
     * @return string   info.tid 订单编号
     * @return string   info.shop_id 商家id
     * @return string   info.trade.status 订单状态
     * @return string   info.trade.created_time 下单时间
     * @return string   info.trade.receiver_name 收货人姓名
     * @return string   info.trade.receiver_address 收货人详细地址
     * @return string   info.trade.receiver_state 收货人所在城市
     * @return string   info.trade.receiver_city 收货人所在地区
     * @return string   info.trade.receiver_mobile 收货人电话
     * @return string   info.trade.receiver_phone 收货人电话
     * @return string   info.sku.pic_path 退换货商品图片地址
     * @return string   info.sku.title 退换货商品标题
     * @return string   info.sku.spec_nature_info 退换货商品属性
     * @return string   info.sku.price 退换货商品价格
     * @return string   info.num 退换货商品数量
     * @return string   info.payment 退换货商品支付价格
     * @return string   info.gift_data 退换货商品赠品信息
     * @return string   info.reason 退换货理由
     * @return string   info.description 问题描述
     * @return string   info.evidence_pic 图片信息
     * @return string   info.shop_explanation 商家处理申请说明
     * @return string   refunds.total_price 退款金额
     * @return string   refunds.refunds_reason 退款备注
     * @return string   info.admin_explanation 平台审核意见
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        $aftersales_bn = trim($params['aftersales_bn']);
        $result = kernel::single('topmanageapi_customer_afterSales')->__getDataInfo($shop_id,$aftersales_bn);
        return $result;
    }
}
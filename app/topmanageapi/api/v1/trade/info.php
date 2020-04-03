<?php
/**
 * topapi
 *
 * -- trade.info
 * -- 订单详情
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_trade_info implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '订单详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'tid'    => ['type'=>'string', 'valid'=>'required', 'example'=>'2035646000200002 ', 'desc'=>'订单编号', 'msg'=>'订单编号必填']
        ];
    }

    /**
     * @return array 订单详情
     */
    public function handle($params)
    {
        //获取订单详情
        $postData = [];
        $postData['tid'] = $params['tid'];
        $postData['fields'] = "shipping_type,orders.spec_nature_info,user_id,tid,status,payment,points_fee,ziti_addr,ziti_memo,post_fee,pay_type,payed_fee,receiver_state,receiver_city,receiver_district,receiver_address,receiver_zip,trade_memo,shop_memo,receiver_name,receiver_mobile,orders.price,orders.num,orders.title,orders.item_id,orders.pic_path,total_fee,discount_fee,buyer_rate,adjust_fee,orders.total_fee,orders.adjust_fee,created_time,pay_time,consign_time,end_time,shop_id,need_invoice,invoice_name,invoice_type,invoice_main,invoice_vat_main,orders.bn,cancel_reason,orders.refund_fee,orders.aftersales_status,orders.gift_data";
        $tradeInfo = app::get('topshop')->rpcCall('trade.get',$postData);
        if (!$tradeInfo) {
            $msg = app::get('topshop')->_('订单信息不存在，原因未知');
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }
        //获取用户名称 
        $userInfo = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => $tradeInfo['user_id']]);
        $tradeInfo['login_account'] = $userInfo[$tradeInfo['user_id']];
        $tradeInfo['logi'] = app::get('topshop')->rpcCall('delivery.get',array('tid'=>$params['tid']));
        $tradeInfo = kernel::single('topmanageapi_trade_tradeListinfo')->__checkTradeinfoParams($tradeInfo);
        return $tradeInfo;
    }
}


<?php

class topmanageapi_api_v1_trade_delivery implements topmanageapi_interface_api {

    public $apiDescription = "对指定订单进行发货";

    public function setParams()
    {
        return array(
            'tid'       => ['type'=>'int', 'valid'=>'required', 'example'=>'','desc'=>'订单号'],
            'corp_code' => ['type'=>'int', 'valid'=>'required','example'=>'','desc'=>'物流公司编号'],
            'corp_no'   => ['type'=>'int', 'valid'=>'', 'example'=>'','desc'=>'运单号'],
            'ziti_memo' => ['type'=>'int', 'valid'=>'', 'example'=>'','desc'=>'自提备注'],
            'memo'      => ['type'=>'string','valid'=>'', 'desc'=>'备注','example'=>'1'],
        );
    }

    public function handle($params)
    {
        $requestParams['tid']       = $params['tid'];//订单号
        $requestParams['corp_code'] = $params['corp_code'];//物流公司编号
        $requestParams['logi_no']   = $params['corp_no'];//运单号
        $requestParams['ziti_memo'] = $params['ziti_memo'];//自提备注
        $requestParams['shop_id']   = $params['oAuthInfo']['shop_id'];//商家id
        $requestParams['seller_id'] = $params['oAuthInfo']['seller_id'];//操作员id
        $requestParams['memo']      = $params['memo'];//自提备注
        $res = app::get('topmanageapi')->rpcCall('trade.delivery',$requestParams);
        return ['result' => true];
    }
}


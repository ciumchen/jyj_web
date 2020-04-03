<?php
class sysshop_api_oauth_sellerLogin{
    public $apiDescription = "用于OAuth登陆商家的接口";
    public function getParams()
    {
        $return['params'] = array(
            'loginname' => ['type'=>'string','valid'=>'','description'=>'卖家用户用户名','default'=>'当前登录的商家','example'=>'1'],
            'password' => ['type'=>'string','valid'=>'','description'=>'卖家用户密码','default'=>'当前登录的商家','example'=>'1'],
        );
        return $return;
    }

    public function login($params)
    {
        $return = [
            'status' => 'success',
            'data' => shopAuth::apiLogin($params['loginname'], $params['password']),
            ];
        $sellerData = shopAuth::getSellerData($return['data']['sellerId']);
        $return['data']['seller_id'] = $return['data']['sellerId'];
        $return['data']['shop_id'] = $sellerData['shop_id'];
        $return['data']['login_account'] = $sellerData['login_account'];
        $return['data']['name'] = $sellerData['name'];
        $return['data']['mobile'] = $sellerData['mobile'];
        $return['data']['email'] = $sellerData['email'];
        return $return;
    }
}



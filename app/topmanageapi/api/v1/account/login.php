<?php
/**
 * topapi
 *
 * -- user.login
 * -- 用户登录
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_account_login implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '用户登录';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'account'  => ['type'=>'string', 'valid'=>'required', 'example'=>'demo',    'desc'=>'登录账号/手机/邮箱', 'msg'=>'请填写登录账号'],
            'password' => ['type'=>'string', 'valid'=>'required', 'example'=>'demo123', 'desc'=>'登录密码',         'msg'=>'请填写密码'],
//          'deviceid' => ['type'=>'string', 'valid'=>'required', 'example'=>'demo123', 'desc'=>'用户设备'],
            'vcode'    => ['type'=>'string', 'valid'=>'',         'example'=>'',        'desc'=>'登录超过3次，出现图片验证码，需要验证图片验证码,图形验证码类型为topmanageapi_login', 'msg'=>'请输入验证码'],
            'session_id' => ['type' => 'string','valid'=>'','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填'],
            'imagevcodekey' => ['type'=>'string','valid'=>'', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topmanageapi_login','msg'=>'图片验证类型必填'],
            'size' => ['type'=>'string','valid'=>'','example'=>'L','desc'=>'店铺图像大小L:大图，M:中图 , S:小图 ,T:微图']
        ];
    }

    /**
     * @return int userId 用户ID
     * @return string accessToken 注册完成后返回的accessToken
     */
    public function handle($params)
    {
        $account = $params['account'];
        $password = $params['password'];
        $vcode_key = $params['imagevcodekey'];
        $size = $params['size'] ? $params['size'] : '';

        $ErrorCountKey = 'topmanageapi_login_error_count'.$params['account'];

        $ErrorCountKey = cache::store('vcode')->get($ErrorCountKey);
        if( $ErrorCount >= 3 )
        {
            if ($params['session_id'] && !empty($params['session_id']))
            {
                kernel::single('base_session')->set_sess_id($params['session_id']);
            }
            //验证图形验证码
            if( !$params['vcode'] || !base_vcode::verify($vcode_key, $params['vcode']))
            {
                throw new \LogicException(app::get('topapi')->_('验证码填写错误'));
            }
        }


        try
        {
//          $result['user_id'] = app::get('topapi')->rpcCall('user.login', ['user_name' => $account, 'password' => $password]);
            $seller = app::get('topmanageapi')->rpcCall('account.shop.oauth.login', ['loginname'=>$account, 'password'=>$password]);

            //app端记录登录日志
//          kernel::single('topapi_passport')->addLoginLog($result['user_id'],$account);

            cache::store('vcode')->put($ErrorCountKey,0);
        }
        catch( Exception $e )
        {
            cache::store('vcode')->put($ErrorCountKey,$ErrorCount+1);
            throw $e;
        }
        $shopdata = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>$seller['data']['shop_id']));
        $seller['data']['shop_logo'] = base_storager::modifier($shopdata['shop_logo'],$size);
        $sellerId = $seller['data']['sellerId'];

        $seller['accessToken'] = kernel::single('topmanageapi_token')->make($sellerId, $seller['data']);
        return $seller;

    }

}


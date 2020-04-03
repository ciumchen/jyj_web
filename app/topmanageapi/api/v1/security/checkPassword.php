<?php
/**
 * topapi
 *
 * -- security.checkpassword
 * -- 用户登录密码验证
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_security_checkPassword implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '用户登录密码验证';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'password' => ['type'=>'string', 'valid'=>'required', 'example'=>'demo123', 'desc'=>'登录密码','msg'=>'请填写密码'],
            'type' => ['type'=>'string','valid'=>'required|in:mobile,email', 'example'=>'email', 'desc'=>'验证账号类型邮箱email手机mobile','msg'=>'请填写账号类型']
//          'deviceid' => ['type'=>'string', 'valid'=>'required', 'example'=>'demo123', 'desc'=>'用户设备']
        ];
    }

    /**
     * @return int userId 用户ID
     * @return string accessToken 注册完成后返回的accessToken
     */
    public function handle($params)
    {
        try {
            $data['seller_id'] = $params['oAuthInfo']['seller_id'];
            $data['type'] = $params['type'];
            $data['login_password'] = $params['password'];
            $result = kernel::single('topmanageapi_security')->doCheckPassword($data);
            if (!$result) {
                return ['status'=>'error','msg'=>'密码填写错误，请重新填写!'];
            }
        } catch ( Exception $e ) {
            $msg = $e->getMessage();
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }

        // 把验证状态放到缓存中
        // 生成随机字符串
        $authArr = array();
        $authArr['auth_str'] = str_random();
        // 对随机字符串进行加密
        $authArr['hash_str'] = hash::make($authArr['auth_str']);
        // 把验证数据存放到缓存中
        cache::store('misc')->add('auth_check_info', serialize($authArr), 60);
        return ['status'=>'success','msg'=>'密码验证成功'];
    }

}


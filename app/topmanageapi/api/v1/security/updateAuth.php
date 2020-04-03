<?php
/**
 * topapi
 *
 * -- security.updateAuth
 * -- 获取验证页数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_security_updateAuth implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '修改认证信息';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'type'          => ['type'=>'string','valid'=>'in:mobile,email', 'example'=>'email', 'desc'=>'验证账号类型邮箱email手机mobile','msg'=>'请填写账号类型'],
            'imgcode'       => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'图片验证码','msg'=>'请填写图片验证码'],
            'ac'            => ['type'=>'string','valid'=>'required', 'example'=>'index', 'desc' =>'操作类型index:验证操作,update:更新操作','msg' =>'参数ac必填'],
            'imagevcodekey' => ['type'=>'string','valid'=>'required', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topshop_bind','msg'=>'图片验证类型必填'],
            'auth_info'     => ['type'=>'string','valid'=>'required', 'example'=>'', 'desc'=>'邮箱地址或者手机号码','msg'=>'邮箱或手机号不能为空'],
            'session_id'    => ['type' => 'string','valid'=>'required','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填'],
            'auth_code'     => ['type'=>'string' ,'valid'=>'required|numeric','example'=>'','desc'=>'邮件或短信验证码','msg'=>'请填写收到的验证码|验证码错误']
        ];
    }

    /**
     * [发送手机或邮箱验证码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
        $postData['vcode_key']  = $params['imagevcodekey'];//图片验证码类型
        $postData['type']       = $params['type'];//验证类型
        $postData['imgcode']    = $params['imgcode'];//图片验证码
        $postData['auth_info']  = $this->__checkAuth($params['auth_info'],$params['type']);//邮箱地址或手机号
        $postData['session_id'] = $params['session_id'];//图片验证码对应session_id
        $postData['auth_code']  = $params['auth_code'];//短信或邮件验证码
        $postData['ac']  = $params['ac'];//短信或邮件验证码
        $seller_id = $params['oAuthInfo']['sellerId'];
        $shop_id = $params['oAuthInfo']['shop_id'];
        if ($params['ac']) {
            switch ($params['ac']) {
                case 'index':
                    $result = kernel::single('topmanageapi_security')->checkAuth($postData,$seller_id,$shop_id,$isAuth = false);
                    break;
                    
                case 'update':
                    $result = kernel::single('topmanageapi_security')->checkAuth($postData,$seller_id,$shop_id);
                    break;
            }
        }
        
        return $result;
    }


    public function __checkAuth($auth_info,$type)
    {
        switch ($type) 
        {
            case 'mobile':
                //验证表单数据
                $validator = validator::make(
                        ['auth_mobile' => $auth_info],
                        ['auth_mobile' => 'required|mobile'],
                        ['auth_mobile' => '手机号必填|手机格式不正确']
                );
                $validator->newFails();
                break;
            case 'email':
                $validator = validator::make(
                        ['auth_mobile' => $auth_info],
                        ['auth_mobile' => 'required|email'],
                        ['auth_mobile' => '邮箱必填|邮箱格式不正确']
                );
                $validator->newFails();
                break;
            case 'default':
                return ['status'=>'error','msg'=>'未知错误'];
                break;
        }
        return $auth_info;
    }
}


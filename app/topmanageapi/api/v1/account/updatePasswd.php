<?php
/**
 * topapi
 *
 * -- user.updatepasswd
 * -- 用户登录通过手机找回密码
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_account_updatePasswd implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '用户登录通过手机找回密码';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'vcode'         => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'图片验证码','msg'=>'请填写图片验证码'],
            'imagevcodekey' => ['type'=>'string','valid'=>'required', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topshop_bind','msg'=>'图片验证类型必填'],
            'mobile'        => ['type'=>'string','valid'=>'required|mobile', 'example'=>'', 'desc'=>'手机号码','msg'=>'手机号不能为空|手机号码格式不正确'],
            'session_id'    => ['type' => 'string','valid'=>'required','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填'],
            'sms_code'         => ['type'=>'string' ,'valid'=>'required|numeric','example'=>'','desc'=>'短信验证码','msg'=>'请填写收到的验证码|验证码错误'],
            'newpasswd'     => ['type'=>'string' , 'valid'=> 'required','example'=>'','desc'=>'新密码','msg'=>'密码不能为空']
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
        $vcode_key = $params['imagevcodekey'];
        $vcode = $params['vcode'];
        $mobile = 
        $tmpltype = 'findPw_shop';
        if ($params['session_id'] && !empty($params['session_id']))
        {
            kernel::single('base_session')->set_sess_id($params['session_id']);
        }
        if ($vcode_key  && $vcode)
        {
            if (! base_vcode::verify ($vcode_key, $vcode))
            {
                return ['status'=>'error','msg'=>'短信验证码错误'];
            }
        }
        if (! userVcode::verify ($params ['sms_code'], $mobile, $tmpltype))
        {
            return ['status'=>'error','msg'=>'短信验证码错误'];
        }
        $requestParams['mobile'] = $params['mobile'];
        $requestParams['seller_type'] = '0';
        $sellerInfo = shopAuth::getFindAuthInfo ($requestParams);
        if (!$sellerInfo)
        {
           return ['status' => 'error','msg'=>'密码重置失败'];
        }

        //开始修改密码
        $sellerId = $sellerInfo['seller_id'];
        $data ['login_password'] = $params ['newpasswd'];
        $data ['psw_confirm'] = $params ['newpasswd'];
        if(shopAuth::resetPwd ($sellerId, $data))
        {
            return ['status'=>'success','mgs'=>'密码修改成功'];
        }
        return ['status'=>'success','mgs'=>'密码重置失败'];
    }
}


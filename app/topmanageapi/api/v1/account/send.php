<?php
/**
 * topapi
 *
 * -- user.send
 * -- 发送短信验证码
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_account_send implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '发送手机验证码';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            /*'vcode' => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'图片验证码','msg'=>'请填写图片验证码'],
            'imagevcodekey' => ['type'=>'string','valid'=>'required', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topshop_bind','msg'=>'图片验证类型必填'],*/
            'mobile' => ['type'=>'string','valid'=>'required', 'example'=>'', 'desc'=>'邮箱地址或者手机号码','msg'=>'邮箱或手机号不能为空']
            /*'session_id' => ['type' => 'string','valid'=>'required','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填']*/

        ];
    }

    /**
     * [发送手机验证码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
        //$vcode_key = $params['imagevcodekey'];
        //$vcode = $params['vcode'];
        $mobile = $params['mobile'];
        $_tmpltype = 'findPw_shop';
        /*if($params['session_id'])
        {
            kernel::single('base_session')->set_sess_id($params['session_id']);
        }
        if ($vcode_key && $vcode)
        {
            if (! base_vcode::verify ($vcode_key, $vcode))
            {
            throw new \LogicException (app::get ('topshop')->_ ('图片验证码错误!'));
            }
        }*/

        if(!userVcode::send_sms ($_tmpltype, $mobile))
            {
                return ['status'=>'error','msg'=>'短信验证码发送失败'];
            }
        return ['status'=>'success','msg'=>'短信验证码发送成功'];
    }
}


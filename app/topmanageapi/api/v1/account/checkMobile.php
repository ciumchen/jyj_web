<?php
/**
 * topapi
 *
 * -- user.checkmobile
 * -- 检测手机号码是否注册
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_account_checkMobile implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '检测手机号是否认证';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'vcode' => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'图片验证码','msg'=>'请填写图片验证码'],
            'imagevcodekey' => ['type'=>'string','valid'=>'required', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topshop_bind','msg'=>'图片验证类型必填'],
            'mobile' => ['type'=>'string','valid'=>'required|mobile', 'example'=>'', 'desc'=>'手机号码','msg'=>'手机号不能为空'],
            'session_id' => ['type' => 'string','valid'=>'required','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填']            
        ];
    }

    /**
     * [验证手机号码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
        $vcode_key = $params['imagevcodekey'];
        $session_id = $params['session_id'];
        $vcode = $params['vcode'];
        $mobile = $params['mobile'];
        if($session_id)
        {
            kernel::single('base_session')->set_sess_id($session_id);
        }
        if ($vcode_key && $vcode)
        {
            if (! base_vcode::verify ($vcode_key, $vcode))
            {
            throw new \LogicException (app::get ('topshop')->_ ('图片验证码错误!'));
            }
        }
        if(!$this->isAuth($mobile))
        {
            return ['status'=>'error','msg'=>'手机号码未认证，请换一个重试'];
        }
        return true;
    }


    public function isAuth($mobile)
    {
        $flag = false;
        $params['seller_type'] = '0';
        $params['mobile'] = $mobile;
        $authInfo = shopAuth::getFindAuthInfo ($params);
        $flag = $this->__proAuthInfo ($authInfo);
        return $flag;
    }

        /**
     * 处理验证数据
     * 
     * @param array $authInfo
     * @param string $type
     * @return bool
     * */
    private function __proAuthInfo($authInfo)
    {

        if (! $authInfo)
        {return false;}
        
        if ($authInfo ['auth_type'] == 'UNAUTH')
        {return false;}
        
        $flag = false;
        if ($authInfo ['auth_type'] == 'AUTH_MOBILE' || $authInfo ['auth_type'] == 'AUTH_ALL')
        {
            $flag = true;
        }

        
        return $flag;
    }

}


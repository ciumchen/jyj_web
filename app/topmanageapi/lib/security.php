<?php

class topmanageapi_security{

    protected $_tmpltype = 'auth_shop';

/**
 * 验证登录密码
 * @AuthorHTL
 * @DateTime  2017-09-19T14:35:00+0800
 * @param     [array]                   $array []
 * @return    [bool]                          
 */
    public function doCheckPassword($array)
    {
        $data = utils::_filter_input($array);
        $accountShopModel = app::get('sysshop')->model('account');
        $filter = array('seller_id'=>$array['seller_id']);
        $account = $accountShopModel->getRow('seller_id,login_password',$filter);
        if(!hash::check($data['login_password'], $account['login_password']))
        {
            return false;
            /*throw new \LogicException(app::get('sysshop')->_('密码填写错误，请重新填写!'));*/
        }

        return true;
    }

/**
 * 安全中心发送短信或邮箱
 * @AuthorHTL
 * @DateTime  2017-09-18T16:56:00+0800
 * @param     [array]                   $params [接口参数]
 * @return    [type]                           [description]
 */
    public function send($params)
    {
        $vcode_key = $params['imagevcodekey'];
        $image_code = $params['imgcode'];
        $seller_id = $params['oAuthInfo']['sellerId'];
        try {
            if ($params['session_id'])
                {
                    kernel::single('base_session')->set_sess_id($params['session_id']);
                }
            if(!base_vcode::verify($vcode_key, $image_code))
                {
                    /*$msg = app::get ('topshop')->_ ('验证码填写错误');
                    throw new \LogicException(app::get('topmanageapi')->_($msg));*/
                    return ['status'=>'error','msg'=>'验证码填写错误'];
                }
            $type = $params['type'];
            // 查看验证类型
            switch ($type) {
                case 'mobile' :
                    $mobile = $params['auth_info'];
                    // 验证所填写的手机号是否已存在
                    $bool = $this->isErrorInfo($type, $mobile, $params['ac'], false,$seller_id);
                    if($bool)
                    {
                        return ['status'=>'error','msg'=>'手机号已存在，请换一个重试'];
                        /*throw new \LogicException (app::get ('topshop')->_ ('手机号已存在，请换一个重试'));*/
                    }
                    if(userVcode::send_sms ($this->_tmpltype, mobile))
                        {
                            return ['status'=>'success','msg'=>'短信验证码发送成功'];
                        }
                    break;
                case 'email' :
                    $email = $params ['auth_info'];
                    // 验证所填写的邮箱是否已存在
                    $bool = $this->isErrorInfo($type, $mobile, $params['ac'], false,$seller_id);
                    if($bool)
                    {
                        return ['status'=>'error','msg'=>'邮箱地址已存在，请换一个重试'];
                        /*throw new \LogicException (app::get ('topshop')->_ ('邮箱地址已存在，请换一个重试'));*/
                    }
                    
                    $contentUrl = '';
                    if(userVcode::send_email ($this->_tmpltype, $email, $contentUrl, false))
                        {
                            return ['status'=>'success','msg'=>'邮件验证码发送成功'];
                        }
                    break;
            }
        } catch (Exception $e) {
            $msg = $e->getMessage ();
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }
    }

/**
 * 验证验证码信息更新数据
 * @AuthorHTL
 * @DateTime  2017-09-19T17:49:58+0800
 * @param     [type]                   $params [description]
 * @param     boolean                  $isAuth [description]
 * @return    [type]                           [description]
 */
    public function checkAuth($params,$seller_id,$shop_id,$isAuth = TRUE)
    {
        $type = $params['type'];
        try {
            // 查看验证类型
            switch ($type) {
                case 'mobile' :
                    $mobile = $params ['auth_info'];
                    if (! userVcode::verify ($params ['auth_code'], $mobile, $this->_tmpltype)) {
                        return ['status'=>'error','msg'=>'验证码错误，请重新填写'];
                        /*throw new \LogicException(app::get('topshop')->_('验证码错误，请重新填写'));*/
                    }
                  
                    // 判断认证状态
                    if($isAuth)
                    {
                        $data ['auth_type'] = $this->__getAuthType ($type,$seller_id);
                    }
                    $data['mobile'] = $mobile;
                    switch ($params['ac']) {
                        case 'index':
                            $msg = '手机绑定成功！';
                            break;
                        
                        case 'update':
                            $msg = '手机修改成功！';
                            break;
                    }
                    break;
                case 'email' :
                    $email = $params ['auth_info'];
                    if (! userVcode::verify ($params ['auth_code'], $email, $this->_tmpltype)) {
                        return ['status'=>'error','msg'=>'验证码错误，请重新填写'];
                        /*throw new \LogicException(app::get('topshop')->_('验证码错误，请重新填写'));*/
                    }
                   
                    // 判断认证状态
                    if($isAuth)
                    {
                        $data ['auth_type'] = $this->__getAuthType ($type,$seller_id);
                    }
                    
                    $data['email'] = $email;
                    switch ($params['ac']) {
                        case 'index':
                            $msg = '邮箱绑定成功！';
                            break;
                        
                        case 'update':
                            $msg = '邮箱修改成功！';
                            break;
                    }
                    break;
            }
            //调用接口修改认证状态
            $data['shop_id'] = $shop_id;
            $data ['seller_id'] = $seller_id;
            if(app::get('topshop')->rpcCall('auth.shop.updata',$data))
                {
                    return ['status'=>'success','msg'=>$msg];
                }
        } catch ( Exception $e ) {
            $msg = $e->getMessage ();
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }
    }

    /**
     *  判断手机或邮箱是否出现重复
     *  
     *  @param string $type
     *  @param string $str
     *  @param string $ac
     *  @param boolean $isJson 
     *  @param string $seller_id 
     * */
    public function isErrorInfo($type, $str, $ac, $isJson = true,$seller_id)
    {
        switch ($ac) {
            case 'index':
                $flag = $this->isAuthExits($str, $type, $ac,$seller_id);
                break;
            case 'update':
                $flag = $this->isAuthExits($str, $type, $ac,$seller_id);
                break;
        }
        
        if(!$isJson)
        {
            return $flag;
        }
        $status =  $flag ? 'false' : 'true';
        $topshopObj = kernel::single('topshop_controller');
        return $topshopObj->isValidMsg($status);
    }


/**
     * 商家安全中心认证信息判断
     *
     * @author Xiaodc
     * @access public
     * @param string $str 用户提交的手机号或邮箱
     * @param string $type 手机类型或邮箱类型
     * @param string $ac 用户操作类型，验证或修改
     * @return boolean
     */
    public function isAuthExits($str, $type, $ac = 'update',$seller_id)
    {
        $acArr = array('index', 'update');
        if(! in_array($ac, $acArr))
        {
            return false;
        }

        if($ac == 'update')
        {
            return $this->isExists($str, $type);
        }

        //检查数据安全
        $str = utils::_filter_input($str);

        if(empty($str)) return false;

        // 在验证的时候只验证店主
        $filter = array('seller_type' => '0');
        switch($type)
        {
            case 'mobile':
                $sysshopModel = app::get('sysshop')->model('seller');
                $filter['mobile'] = trim($str);
                $data = $sysshopModel->getRow('seller_id', $filter);
                break;
            case 'email':
                $sysshopModel = app::get('sysshop')->model('seller');
                $filter['email'] = trim($str);
                $data = $sysshopModel->getRow('seller_id', $filter);
                break;
        }
        $bool = false;
        if($data['seller_id'] && $data['seller_id'] != $seller_id)
        {
            $bool = true;
        }

        return $bool;
    }

/**
     * @brief 判断注册信息账号，手机号，邮箱是否已近注册
     *
     * @param string $str 验证字符串
     * @param string $type 验证类型 账号，手机号，邮箱
     *
     * @return bool true已存在 | false不存在
     */
    public function isExists($str, $type='account')
    {
        //检查数据安全
        $str = utils::_filter_input($str);

        if(empty($str)) return false;

        switch($type)
        {
            case 'account':
                $accountShopModel = app::get('sysshop')->model('account');
                $data = $accountShopModel->getRow('seller_id',array('login_account'=>trim($str)));
                break;
            case 'mobile':
                $sysshopModel = app::get('sysshop')->model('seller');
                $data = $sysshopModel->getRow('seller_id',array('mobile'=>trim($str), 'auth_type|in'=>['AUTH_ALL', 'AUTH_MOBILE']));
                break;
            case 'email':
                $sysshopModel = app::get('sysshop')->model('seller');
                $data = $sysshopModel->getRow('seller_id',array('email'=>trim($str), 'auth_type|in'=>['AUTH_ALL', 'AUTH_EMAIL']));
                break;
        }
        return $data['seller_id'] ? true : false;
    }

/**
     * 获取认证状态
     * @param string $type
     * @return string 
     * */
    private function __getAuthType($type,$seller_id)
    {

        $sellinfo = shopAuth::getSellerData ($seller_id);
        $authType = $sellinfo ['auth_type'];
        switch ($authType) {
            case 'UNAUTH' :
                if ($type == 'mobile') {
                    $authType = 'AUTH_MOBILE';
                }
                if ($type == 'email') {
                    $authType = 'AUTH_EMAIL';
                }
                break;
            case 'AUTH_MOBILE' :
                if ($type == 'email') {
                    $authType = 'AUTH_ALL';
                }
                break;
            case 'AUTH_EMAIL' :
                if ($type == 'mobile') {
                    $authType = 'AUTH_ALL';
                }
                break;
            case 'AUTH_ALL' :
                return $authType;
                break;
        }
        
        return $authType;
    }    
}
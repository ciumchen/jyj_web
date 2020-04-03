<?php
class sysuser_ctl_admin_check extends desktop_controller
{

    function __construct($app)
    {
        parent::__construct($app);
        $this->pamUserModel = app::get('sysuser')->model('account');
        $this->sysUserModel = app::get('sysuser')->model('user');
    }

    public function index()
    {
        $userId = input::get('id');
        $account = $this->pamUserModel->getList('*', ['user_id' => $userId]);
        $user = $this->sysUserModel->getList('*', ['user_id' => $userId]);
        $pagedata['user'] = $user[0] ?: [];
        $pagedata['account'] = $account[0] ?: [];
        $pagedata['singlepage'] = true;
        
        $examineData= redis::scene('sysuser')->lrange('user_id_'.$userId,0,-1);
        $pagedata['examineLog'] = array();
        foreach ($examineData as $key => $value) {
            $pagedata['examineLog'][$key] = unserialize($value);
        }
        
        // print_r($pagedata);
        $pagedata['examine_setting'] = app::get('sysconf')->getConf('shop.goods.examine');
        return $this->singlepage('sysuser/admin/user/detail_check.html', $pagedata);
    }

    public function approve()
    {
        $data = input::get();
        $data['user_id'] = intval($data['user_id']);
        $data['check_status'] = intval($data['check_status']);
        
        if (!$data['user_id']) {
            return $this->splash('error',null,'用户参数错误',true);
        }
        if( !trim($data['reason']) && $data['check_status'] == '2' )
        {
            return $this->splash('error',null,'请填写驳回原因',true);
        }

        $logInfo=array(
            'time' => time(),
            'check_status' => $data['check_status'],
            'reason' => $data['reason']
        );
        
        try{
            $result =  $this->pamUserModel->update(['check_status' => $data['check_status']], ['user_id' => $data['user_id']]);
            if ($result) {
                $this->adminlog("用户审核状态[{$data['check_status']}]，用户手机：{$data['mobile']}", 1);
                redis::scene('sysuser')->rpush('user_id_'.$data['user_id'],serialize($logInfo));

                //短信通知
                if ($data['check_status'] == 1){
                    $result = messenger::sendSms($data['mobile'], 'account-reg-check-succ', []);
                    if($result['rsp'] == "fail")
                    {
                        logger::info('注册审核通过短信通知发送失败',$result);
                    }
                }elseif($data['check_status'] == 2) {
                    $result = messenger::sendSms($data['mobile'], 'account-reg-check-fail', ['reason' => $logInfo['reason']]);
                    if($result['rsp'] == "fail")
                    {
                        logger::info('注册审核不通过短信通知发送失败',$result);
                    }
                }
                return $this->splash('success',null,'操作成功',true);
            }else{
                $this->adminlog("商品审核[{$data['check_status']}]，商品ID：{$data['mobile']}", 0);
                return $this->splash('error',null,'操作失败',true);
            }
        } catch(\LogicException $e){
            return $this->splash('success',null,$e->getMessage(),true);
        }
    }

    public function refuse()
    {
        $pagedata = input::get();
        return view::make('sysuser/admin/user/refuse.html', $pagedata);
    }

    public function doSetting()
    {
    }
}

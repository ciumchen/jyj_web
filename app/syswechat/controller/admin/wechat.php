<?php

class syswechat_ctl_admin_wechat extends desktop_controller
{
    public function index()
    {
        $actions = array(
            array(
                'label'=>app::get('syswechat')->_('新增'),
              //'icon' => 'download.gif',
                'target'=>'dialog::{ title:\''.app::get('syswechat')->_('新增一个平台微信账号').'\', width:800, height:600}',
                'href' => '?app=syswechat&ctl=admin_wechat&act=add',
            ),
        );
        return $this->finder('syswechat_mdl_wechat',array(
            'use_buildin_set_tag' => false,
            'use_buildin_tagedit' => false,
            'use_buildin_filter'=> true,
            'use_buildin_refresh' => true,
            'use_buildin_delete' => true,
            //'allow_detail_popup' => true,
            'title' => app::get('syswechat')->_('微信公众号列表'),
            'actions' => $actions,
        ));
    }

    public function add()
    {
        $id = kernel::single('syswechat_wechat')->addWechat('platform');
        return $this->edit($id);
    }

    public function edit($id = null)
    {
        if(is_null($id))
            $id = input::get('id');
        $wechat = kernel::single('syswechat_wechat')->getWechatInfoById($id);
        $pagedata = ['data' => $wechat];
        $pagedata['weixin_type_select'] = array('subscription'=>'订阅号','service'=>'服务号');
        $pagedata['url'] = kernel::single('syswechat_wechat')->genServerUrl($wechat['eid']);
        return $this->page('syswechat/admin/wechat/edit.html',$pagedata);
    }

    public function save()
    {
        $requestParams = input::get();
        if($requestParams['id'] < 1) return $this->splash('error',null,'提交数据错误:缺少id');

        $wechatInfo = kernel::single('syswechat_wechat')->getWechatInfoById($requestParams['id']);
        $wechat = [
            'id'             => $requestParams['id'],
            'eid'            => $requestParams['eid'],
            'type'           => $wechatInfo['type'],
            'wechat_type'    => $requestParams['wechat_type'],
            'title'          => $requestParams['title'],
            'wechat_account' => $requestParams['wechat_account'],
            'wechat_id'      => $requestParams['wechat_id'],
            'email'          => $requestParams['email'],
            'app_id'         => $requestParams['app_id'],
            'secret'         => $requestParams['secret'],
            'token'          => $requestParams['token'],
            'qrcode'         => $requestParams['qrcode'],
            'aes_key'        => $requestParams['aes_key'],
        ];
        $wechatInfo = kernel::single('syswechat_wechat')->editWechat($wechat);

        return $this->splash('success',null,'保存成功');
    }

}

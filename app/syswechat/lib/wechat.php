<?php

class syswechat_wechat
{

    public function getWechatInfoByEid($eid)
    {
        $wechatModel = app::get('syswechat')->model('wechat');
        $wechat = $wechatModel->getRow('*', ['eid'=>$eid]);

        return $wechat;
    }

    public function getWechatInfoById($id)
    {
        $wechatModel = app::get('syswechat')->model('wechat');
        $wechat = $wechatModel->getRow('*', ['id'=>$id]);

        return $wechat;
    }

    public function addWechat($type = 'platform')
    {
        $wechat = [
            'type' => $type,
            'token' => $this->genToken(),
        ];

        $wechatModel = app::get('syswechat')->model('wechat');
        $id = $wechatModel->insert($wechat);

        $wechat['id'] = $id;
        $wechat['eid'] = $type . $id;
        $wechatModel->update($wechat, ['id'=>$id]);

        return $id;
    }

    public function editWechat($wechatInfo)
    {
        $id = $wechatInfo['id'];
        $wechatModel = app::get('syswechat')->model('wechat');
        $wechatModel->update($wechatInfo, ['id'=>$id]);
        return true;
    }

    private function genToken()
    {
        $token = md5(microtime() . rand(1000, 9999));
        return $token;
    }

    public function genServerUrl($eid)
    {
        return url::action('syswechat_server@process', ['eid'=>$eid]);
    }

}


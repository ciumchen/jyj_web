<?php
class topshop_ctl_shop_setting extends topshop_controller{

    public function index()
    {
        $shopdata = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>shopAuth::getShopId()),'seller');
        //只需要地址的数字代码
        $area = explode(':', $shopdata['shop_area']);
        $shopdata['area'] = $area[1];
        $shopdata['shop_area'] = $area[0];
        $addr_switch = ['open','close'];
        //判断是否设置地区配置 没有则默认为关闭
        if (empty($shopdata['addr_switch']) || !in_array($shopdata['addr_switch'],$addr_switch))
        {
            $shopdata['addr_switch'] = 'close';
        }
        //unset($shopdata['shop_area']);
        $pagedata['shop'] = $shopdata;
        $pagedata['im_plugin'] = app::get('sysconf')->getConf('im.plugin');
        $pagedata['baiDuapiAk'] = app::get('syslogistics')->getConf('baiDuapiAk');
        $this->contentHeaderTitle = app::get('topshop')->_('店铺设置');
        return $this->page('topshop/shop/setting.html', $pagedata);
    }

    public function saveSetting()
    {
        $postData = input::get();
        $validator = validator::make(
            [$postData['shop_descript'],$postData['addr_switch']],['max:200','in:close,open'],['店铺描述不能超过200个字符!','地区配置信息有误!']
        );
        if ($validator->fails())
        {
            $messages = $validator->messagesInfo();
            foreach( $messages as $error )
            {
                return $this->splash('error',null,$error[0]);
            }
        }
        try
        {
            $postData['shop_area'] = $postData['selected_addr'].':'.$postData['area'][0];
            $result = app::get('topshop')->rpcCall('shop.update',$postData);
            if( $result )
            {
                $msg = app::get('topshop')->_('设置成功');
                $result = 'success';
            }
            else
            {
                $msg = app::get('topshop')->_('设置失败');
                $result = 'error';
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            $result = 'error';
        }
        $this->sellerlog('编辑店铺配置。如店铺logo,描述等');
        $url = url::action('topshop_ctl_shop_setting@index');
        return $this->splash($result,$url,$msg,true);

    }

}



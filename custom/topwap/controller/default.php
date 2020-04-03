<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topwap_ctl_default extends topwap_controller
{
    public function index()
    {
        $GLOBALS['runtime']['path'][] = array('title'=>app::get('topwap')->_('首页〉'),'link'=>kernel::base_url(1));
        $this->setLayoutFlag('index');

        // 检测是否登录
        if( !userAuth::check() )
        { 
            if( request::ajax() )
            {
                $url = url::action('topwap_ctl_passport@goLogin');
                return $this->splash('error', $url, app::get('topwap')->_('请登录'), true);
            }
            redirect::action('topwap_ctl_passport@goLogin')->send();exit;
        }		 
        return $this->page("topwap/index.html");
    } 

    public function switchToPc()
    {
        setcookie('browse', 'pc');
        return redirect::route('topc');
    }
}

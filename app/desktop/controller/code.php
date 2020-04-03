<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class desktop_ctl_code extends base_routing_controller
{
    public function __construct($app)
    {
    }

    //激活码校验
    public function codecheck()
    {
        //if ($_POST['auth_code'] && preg_match("/^\d{19}$/", substr($_POST['auth_code'], 1)))
        if ($_POST['auth_code'])
        {
            $code = kernel::single('base_license_active');
            $result = $code->active($_POST['auth_code']);
            if ($result['res'] == 'succ')
            {
                $activation_arr = $_POST['auth_code'];
                app::get('desktop')->setConf('activation_code', $activation_arr);

                $objArr = kernel::servicelist("desktop.cert.succ");
                foreach ($objArr as $obj)
                {
                    if(method_exists($obj , 'notify')){
                        $obj->notify($result);
                    }
                }

                header('Location:' .url::route('shopadmin'));
                exit;
            }
            else
            {
                die($this->error_view($result['message_zh'], $result['url']));
            }

            header("Location: index.php");
            exit();
        }
    }

    function error_view($auth_error_msg, $info_url = null, $error_code = '')
    {
        $pagedata['res_url'] = app::get('desktop')->res_url;
        $pagedata['info_url'] = $info_url;
        $pagedata['auth_error_msg'] = $auth_error_msg;
        return view::make('desktop/active_code.html', $pagedata);
    }
}

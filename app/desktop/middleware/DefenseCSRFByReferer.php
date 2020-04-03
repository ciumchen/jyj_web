<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_middleware_DefenseCSRFByReferer
{
    public function __construct()
    {

    }

    public function handle($request, Closure $next)
    {
        if(config::get('security.desktop.defenseCSRFByReferer', false))
        {
            $requestParams = input::get();
            $ctl = $requestParams['ctl'];

            $method = input::server('REQUEST_METHOD');
            if(count($requestParams) == 0) {
                //没有任何传参的话让他过吧。
            }elseif($ctl == 'passport' && $method == 'GET'){
                //登录页不是POST传参也让过吧。。。。
            }else{
                $referer = input::server('HTTP_REFERER');
                $refererParams = parse_url($referer);
                $refererHost = $refererParams['host'];
                $refererPort = empty($refererParams['port']) ? 80 : $refererParams['port'];
                $host    = input::server('HTTP_HOST');
                if($refererHost != $host && $refererHost . ":" . $refererPort != $host)
                {
                    echo "检查到CSRF攻击，请谨慎打开未知链接！\n";
                    echo "跳转来自网站：$refererHost \n";
                    echo "跳转来自网站端口：$refererPort \n";
                    echo "请求方向网站：$host";
                    exit;
                }
            }
        }

        return $next($request);
    }

}

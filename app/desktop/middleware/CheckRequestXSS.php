<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class desktop_middleware_CheckRequestXSS
{
    public function __construct()
    {

    }

    public function handle($request, Closure $next)
    {
        if(config::get('security.desktop.globalCheckXSSInject', false))
        {
            $requestParams = input::get();
            try{
                $this->checkRequest($requestParams);
            }catch(Exception $e){
                $ctl = new desktop_controller();
                return $ctl->splash('error',$url=null,$e->getMessage());
            }
        }

        return $next($request);
    }

    public function checkRequest($data)
    {

        if(is_array($data)){
            foreach($data as $key=>$v){
                $data[$key] = $this->checkRequest($data[$key]);
            }
        }else{
            if(strlen($data)){
                $tmpdata = $data;
                $data = utils::_RemoveXSS($data);
                if($data != $tmpdata)
                {
                    throw new Exception("本次提交的内容含有XSS攻击脚本，如果是真实业务需求，请联系系统管理员，在config/security.php中关闭相关选项");
                }
            }else{
                $data = $data;
            }
        }
        return $data;

    }
}

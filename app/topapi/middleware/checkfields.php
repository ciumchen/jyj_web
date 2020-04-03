<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topapi_middleware_checkfields
{

    public function __construct()
    {

    }

    public function handle($request, Closure $next)
    {
        $requestParams = $request->all();
        try{
            foreach($requestParams as $key=>$value)
            {
                if(strstr($key, 'fields'))
                {
                    kernel::single('topapi_utils_checkfields')->checkfields($value);
                }
            }
        }catch(Exception $e){
            if(config::get('app.debug'))
                return response::json(array(
                    'errorcode' => 10000,
                    'msg' => $e->getMessage(),
                ));
            else
                return response::json(array(
                    'errorcode' => 10000,
                    'msg' => 'Chunjie holiday also don\'t let people good rest',
                ));
        }
        return $next($request);
    }
}

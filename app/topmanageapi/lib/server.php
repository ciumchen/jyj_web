<?php

class topmanageapi_server
{

    /**
     * 是否需要验证AccessToken
     */
    public $isCheckAccessToken = false;

    public function process()
    {
        $params = input::get();
        try
        {
            $version = input::get('v');
            if( !$version )
            {
                throw new RuntimeException(app::get('topmanageapi')->_('系统参数：版本号必填'), '10001');
            }

            $this->setReturnFormat(input::get('format'));

            $objApiClass = $this->getApiClassByMethod(input::get('method'), $version);

            if( $this->isCheckAccessToken )
            {
                $oAuthInfo = $this->checkAccessToken(input::get('accessToken'));
                $accessToken = input::get('accessToken');
            }

            //验证api调用参数
            $apiParams = $this->parseParams($objApiClass[0], $params);
            if( $oAuthInfo ) $apiParams['oAuthInfo'] = $oAuthInfo;
            if( $accessToken ) $apiParams['accessToken'] = $accessToken;

            //运行
            $response = $this->run($objApiClass, $apiParams);
        }
        catch( \LogicException $e )
        {
            return $this->__sendError($e->getMessage(), $e->getCode());
        }
        catch( \RuntimeException $e )
        {
            if (config::get('app.debug'))
            {
                $msg = $e->getMessage();
            }
            else
            {
                $msg = '系统繁忙，请重试';
            }
            return $this->__sendError($e->getMessage(), $e->getCode());
        }
        catch( \Exception $e)
        {
            if (config::get('app.debug'))
            {
                $msg = $e->getMessage();
            }
            else
            {
                $msg = '系统错误，服务暂不可用，请联系平台';
            }

            return $this->__sendError($msg, $e->getCode());
        }

        if( is_string($response) )
        {
            if (config::get('app.debug'))
            {
                $msg = '返回数据不能为字符串，请改为数组';
            }
            else
            {
                $msg = '系统繁忙，请重试';
            }
            return $this->__sendError($msg);
        }

        return $this->response($response);
    }

    private function __sendError($msg, $code)
    {
        if( !$msg ) $msg = 'API调用错误，必须返回错误信息';

        if( !$code ) $code = '10000';

        return $this->response('', $msg, $code);
    }

    /**
     * response
     *
     * @param  boolean $realpath
     * @return base_view_object_interface | string
     */
    final public function response($data, $msg='', $code=0 )
    {
        $result = [ 'errorcode' => $code, 'msg' => $msg, 'data' => $data ];

        switch($this->format) {
            case 'json':
                kernel::single('topmanageapi_format_json')->formatData($result);
                break;
            case 'xml':
                kernel::single('topmanageapi_format_xml')->formatData($result);
                break;
            case 'jsonp':
                kernel::single('topmanageapi_format_jsonp', $this->params['callback'])->formatData($result);
                break;
            default:
                kernel::single('topmanageapi_format_json')->formatData($result);
        }
    }

    public function run($objApiClass, $params)
    {
        return call_user_func($objApiClass, $params);
    }

    public function getApiClassByMethod($method, $version)
    {
        $method = trim($method);

        $topmanageapi = config::get('topmanageapi.routes.'.$version);
        if( !$topmanageapi )
        {
            throw new RuntimeException('该版本号不存在API', 10002);
        }

        if( !in_array($method, array_keys($topmanageapi)) )
        {
            throw new RuntimeException('找不到API:' . $method);
        }

        $this->isCheckAccessToken = $topmanageapi[$method]['auth'] ? true : false;

        list($class, $fun) = $this->parseClassCallable($topmanageapi[$method]['uses']);
        $objclass = new $class();
        if(! $objclass instanceof topmanageapi_interface_api)
        {
            throw new RuntimeException($objclass.' must implements the topmanageapi_interface_api', 10004);
        }

        //判断下方法是否存在
        if( !method_exists( $objclass, $fun ) )
        {
            throw new RuntimeException('找不到方法 :' . $fun, 10003);
        }

        return [$objclass, $fun];
    }

    protected function parseClassCallable($apiHandler)
    {
        $segments = explode('@', $apiHandler);

        return [$segments[0], count($segments) == 2 ? $segments[1] : 'handle'];
    }

    /**
     * 处理参数，并且验证参数
     */
    public function parseParams($class, $params)
    {
        $data = ecos_parseApiParams($class, $params);
        return $data;
    }

    public function checkAccessToken($accessToken)
    {
        $oAuthInfo = kernel::single('topmanageapi_token')->check($accessToken);
        if( !$oAuthInfo )
        {
            throw new \RuntimeException('invalid token', 20001);
        }
        return $oAuthInfo;
    }

    public function setReturnFormat($format)
    {
        $this->format = $format ? $format : 'json';
    }

}

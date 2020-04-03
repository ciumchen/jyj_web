<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class syswechat_server
{
    public function process()
    {
        //      $options = kernel::single('syswechat_options')->getOptions('platform');
        //      $app = new \EasyWeChat\Foundation\Application($options);
        $eid = input::get('eid');
        $redis = redis::scene('syswechat');
        logger::debug('Wechat Server Get Request:'.json_encode(input::get()));
        try{
            $app = kernel::single('syswechat_application', $eid);
            $server = $app->server;
            //这里发现一个鬼畜的问题，如果不exit掉，后面的respose会报错。
            $server->setMessageHandler(function ($message) use($eid, $redis){
                $params = [
                    'eid' => $eid,
                    'message' => $message
                ];
                $redis->rpush('wechat_event_messages', json_encode($params));
                return null;
            });
            $response = $server->serve();
            $response->send();

        }catch(Exception $e){
            logger::error($e->__toString());
            $response->send();
        }

        exit;
    }

}

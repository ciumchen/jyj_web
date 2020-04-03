<?php

class topmanageapi_logger
{
    /**
     * [addLog 添加商家操作日志]
     * @param [type] $memo   [改操作的描述信息]
     * @param [type] $params [api接口参照包含登录信息]
     */
    public function addLog($memo,$params)
    {
        // 判断是否有登录信息
        if (!$params['oAuthInfo'])
        {
            return false;
        }

        //操作信息不存在时返回接口信息
        if (!$memo || empty($memo))
        {
            $memo = '商家app操作:操作未知,操作api为'.input::get('method');
        }
        // 开启了才记录操作日志
        if ( SELLER_OPERATOR_LOG !== true ) return;
        $queue_params = array(
                'seller_userid'     => $params['oAuthInfo']['sellerId'],
                'seller_username'   => $params['oAuthInfo']['loginAccount'],
                'shop_id'           => $params['oAuthInfo']['shop_id'],
                'created_time'      => time(),
                'memo'              => $memo,
                'router'            => request::fullurl(),
                'ip'                => request::getClientIp(),
            );
        return system_queue::instance()->publish('system_tasks_sellerlog', 'system_tasks_sellerlog', $queue_params);
    }
}
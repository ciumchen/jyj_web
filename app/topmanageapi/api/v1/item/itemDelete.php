<?php
/**
 * topapi
 *
 * -- item.delete
 * -- 商品删除
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_itemDelete implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商品删除操作';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'item_id'    => ['type'=>'integer', 'valid'=>'required', 'example'=>'80', 'desc'=>'商品id', 'msg'=>'']
        ];
    }

    /**
     * @AuthorHTL
     * @DateTime  2017-08-23T11:41:09+0800
     * @param     [array] item_id  商品id
     * @return    [array] 删除状态
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        //订单状态
        $orderStatus = array('WAIT_BUYER_PAY', 'WAIT_SELLER_SEND_GOODS', 'WAIT_BUYER_CONFIRM_GOODS');
        try {
        //判断商品所在的订单状态
            $orderParams = array();
            $orderParams['item_id'] = (int)$params['item_id'];
            $orderParams['fields'] = 'status';
            $orderList = app::get('topshop')->rpcCall('trade.order.list.get', $orderParams);
            if ($orderList) {
                $orderArrStatus = array_column($orderList, 'status');
                foreach ($orderStatus as $status)
                {
                    if(in_array($status, $orderArrStatus))
                    {
                        $msg = app::get('topshop')->_('商品存在未完成的订单，不能删除');
                        throw new \LogicException(app::get('topmanageapi')->_($msg));
                    }
                }
            }
            app::get('topshop')->rpcCall('item.delete',array('item_id'=>intval($params['item_id']),'shop_id'=>intval($shop_id)));
        } catch (Exception $e) {
            throw $e;
        }
        return array('status' => 'success', 'message' =>'删除成功');
    }
}


<?php
/**
 * topapi
 *
 * -- item.status
 * -- 商品上下架
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_itemStatus implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商品上下架操作';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'item_id'    => ['type'=>'integer', 'valid'=>'required', 'example'=>'80', 'desc'=>'商品id', 'msg'=>''],
            'type'       => ['type'=>'string','valid'=>'in:tosale,tostock','description'=>'上架(tosale)或者下架(tostock)']
        ];
    }

    /**
     * @return array 商品上下架成功的massage
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        	if ($params['type'] == 'tosale') {
        		$shopdata = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>$shop_id),'seller');
        		if (empty($shopdata) || $shopdata['status'] == 'dead') 
        		{
        			throw new \LogicException(app::get('topmanageapi')->_('抱歉，您的店铺处于关闭状态，不能发布(上架)商品'));
        		}
        		if(app::get('sysconf')->getConf('shop.goods.examine')){
                    $status = 'pending';
                    $msg = app::get('topmanageapi')->_('提交审核成功');
                }else{
                    $status = 'onsale';
                    $msg = app::get('topmanageapi')->_('上架成功');
                }
        	}elseif ($params['type'] == 'tostock') {
        		$status = 'instock';
        		$msg = app::get('topmanageapi')->_('下架成功');
        	}else
        	{
        		throw new \LogicException(app::get('topmanageapi')->_('非法操作'));
        	}

        	$itemstatus = app::get('topc')->rpcCall('item.get',array('item_id'=>$params['item_id'],'fields'=>'item_id,approve_status'));
            if($status =='instock' || $itemstatus['approve_status'] != 'onsale' ){
                $params['item_id'] = intval($params['item_id']);
                $params['shop_id'] = intval($shop_id);
                $params['approve_status'] = $status;
                app::get('topshop')->rpcCall('item.sale.status',$params);
            } 
            return array('status' => 'success', 'message' => $msg );
        }
}


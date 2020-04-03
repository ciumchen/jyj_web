<?php
/**
 * topapi
 *
 * -- item.getstorepolice
 * -- 获取库存报警
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_getStorePolice implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取库存报警信息';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        $data['shop_id'] = $shop_id;
        $data['fields'] = 'police_id,policevalue';
        $storePolice = app::get('topshop')->rpcCall('item.store.info',$data);
        return $storePolice;
    }

}

<?php
/**
 * topapi
 *
 * -- item.savestorepolice
 * -- 设置库存报警
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_saveStorePolice implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '设置库存报警';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'police_id' => ['type'=>'int','valid'=>'', 'example'=>'1', 'desc'=>'库存报警id','msg'=>'police_id不能为空'],
            'policevalue'    => ['type'=>'int', 'valid'=>'required|min:1|max:99999', 'example'=>'10', 'desc'=>'库存报警预警值', 'msg'=>'库存预警值必填!|库存预警值必须为整数!|库存预警值最小为1!|库存预警值最大为99999!'],
        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $paramsData['shop_id'] = $params['oAuthInfo']['shop_id'];
        $paramsData['policevalue'] = $params['policevalue'];
        if(!is_null($params['police_id']))
        {
            $paramsData['police_id'] = $params['police_id'];
        }
        try {
            if(app::get('topshop')->rpcCall('item.store.police.add',$paramsData))
                {
                    return ['status'=>'success','msg'=>'保存成功'];
                }
        } catch (Exception $e) {
            $msg  = $e->getMessage();
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }
    }

}


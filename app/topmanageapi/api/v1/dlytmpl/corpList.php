<?php
class topmanageapi_api_v1_dlytmpl_corpList implements topmanageapi_interface_api {
    /**
     * 接口作用说明
     */
    public $apiDescription = '获取商家快递列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'fields'  => ['type'=>'string', 'valid'=>'', 'example'=>'shop_id,name,template_id',    'desc'=>'字段名称', 'msg'=>'']
        ];
    }

    /**
     * @return json array
     */
    public function handle($params)
    {
        $tmpParams = array(
            'shop_id' => $params['oAuthInfo']['shop_id'],
            'fields' => $params['fields'] ? : '*',
        );
        $ret['dlycorps'] = app::get('topshop')->rpcCall('shop.dlycorp.getlist',$tmpParams);

        return $ret;
    }

}

<?php
class topmanageapi_api_v1_example implements topmanageapi_interface_api {
    /**
     * 接口作用说明
     */
    public $apiDescription = '示例api';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [];
    }

    /**
     * @return json array
     */
    public function handle($params)
    {
        $data = ['aa'=>'bb'];
        return $data;
    }

}

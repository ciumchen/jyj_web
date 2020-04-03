<?php
class topmanageapi_api_v1_category_getPlatformCategoryProps implements topmanageapi_interface_api {
    /**
     * 接口作用说明
     */
    public $apiDescription = '获取平台分类关联的属性';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'cat_id'  => ['type'=>'int', 'valid'=>'required', 'example'=>'1',    'desc'=>'分类编号（cat_id ）', 'msg'=>'请填写分了你编号'],
        ];
    }

    /**
     * @return json array
     */
    public function handle($params)
    {
        $catId = $params['cat_id'];
        $params['cat_id'] = $catId;
        $ret['props'] = app::get('topmanageapi')->rpcCall('category.catprovalue.get',array('cat_id'=>$catId,'type'=>'spec'));
        $ret['props'] = $this->__removeKey($ret['props']);
        return $ret;
    }

    private function __removeKey($array)
    {
        $fmtArray = [];
        foreach($array as $key=>$value)
        {
            if(is_array($value['prop_value']))
                $value['prop_value'] = $this->__removeKey($value['prop_value']);
            $fmtArray[] = $value;
        }
        return $fmtArray;
    }

}

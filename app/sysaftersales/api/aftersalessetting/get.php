<?php

class sysaftersales_api_aftersalessetting_get {

    /**
     * aftersales.setting.get
     * 接口作用说明
     */
    public $apiDescription = '获取指定类目退货换货设置详情';
    public $use_strict_filter = true; // 是否严格过滤参数

    public function getParams()
    {
        $return['params'] = array(
            'cat_id' => ['type'=>'string','valid'=>'required', 'description'=>'类目ID'],
        );
        return $return;
    }

    public function get($params)
    {
        $return = kernel::single('sysaftersales_setting')->getAftersalesSettingByCatId($params['cat_id']);
        return $return;
    }
}

<?php
/**
 * topapi
 *
 * -- index.countdata
 * -- app首页统计数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_shop_readSetting implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '读取店铺配置';

    /**
     * 哪些选项可以发送给前台
     */
    private $range = ['shop_descript', 'qq', 'wangwang', 'shop_logo', 'shop_name', 'shopname'];

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
        ];
    }

    /**
     * @return array shopSetting 店铺配置
     * @return int shopSetting.shop_descript 店铺详情
     * @return string shopSetting.qq 客服店铺的qq号
     * @return string shopSetting.wangwang 客服店铺的旺旺号
     * @return string shopSetting.shop_logo 客服店铺logo 图片地址
     * @return array globalEnv 平台配置
     * @return string globalEnv.im_plugin 当前系统中的客户插件(toputil_im_plugin_wangwang 旺旺；toputil_im_plugin_qq QQ； toputil_im_plugin_webcall 365webcall)
     */
    public function handle($params)
    {
        $shopId = $params['oAuthInfo']['shop_id'];
        $shopdata = app::get('topmanageapi')->rpcCall('shop.get',array('shop_id'=>$shopId));
        $result = [];
        $result['shopSetting'] = $this->itemFilter($shopdata);
        $result['globalEnv']['im_plugin'] = app::get('sysconf')->getConf('im.plugin');
        return $result;
    }

    //这里不显示过多数据，以免被读取到
    public function itemFilter($setting)
    {
        foreach($setting as $k=>$v)
            if(!in_array($k, $this->range))
                unset($setting[$k]);

        return $setting;
    }

}



<?php
/**
 * topapi
 *
 * -- user.logo
 * -- 用户退出登录
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_account_logout implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '用户退出登录';

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
     * @return int userId 用户ID
     * @return string accessToken 注册完成后返回的accessToken
     */
    public function handle($params)
    {
         return kernel::single('topmanageapi_token')->delete($params['accessToken']);
    }

}


<?php
/**
 * topapi
 *
 * -- vcode.genvcode
 * -- 获取验证码
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_vcode_genVcode implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家app获取图片验证码';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'vcode_type' => ['type'=>'string', 'valid'=>'required', 'example'=>'topapi_login', 'desc'=>'验证码类型topshop_bind','msg'=>'图片验证类型必填']
        ];
    }

    /**
     * [获取图片验证码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
       $vcode = kernel::single('base_vcode');
       $vcode->setPicSize(35, 120);
       $vcode->length(4);
       $vcode->verify_key($params['vcode_type']);
       $sess_id = kernel::single('base_session')->sess_id();
       $result['vcode'] = $vcode->base64Image();
       $result['sess_id'] = $sess_id;
       return $result;
    }

}


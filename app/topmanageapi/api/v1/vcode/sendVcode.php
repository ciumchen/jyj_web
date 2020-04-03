<?php
/**
 * topapi
 *
 * -- vcode.sendvcode
 * -- 发送验证码
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_vcode_sendVcode implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家app发送验证码';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'type' => ['type'=>'string','valid'=>'in:mobile,email', 'example'=>'email', 'desc'=>'验证账号类型邮箱email手机mobile','msg'=>'请填写账号类型'],
            'imgcode' => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'图片验证码','msg'=>'请填写图片验证码'],
            'ac' => ['type'=>'string','valid'=>'required', 'example'=>'1111', 'desc'=>'验证为index,更新为update,默认index','msg'=>'请填写图片验证码'],
            'imagevcodekey' => ['type'=>'string','valid'=>'required', 'example'=>'topshop_bind', 'desc'=>'图片验证码key:topshop_bind','msg'=>'图片验证类型必填'],
            'auth_info' => ['type'=>'string','valid'=>'required', 'example'=>'', 'desc'=>'邮箱地址或者手机号码','msg'=>'邮箱或手机号不能为空'],
            'session_id' => ['type' => 'string','valid'=>'required','example'=>'','desc'=>'图片验证码对应的session_id','msg'=>'session_id必填']

        ];
    }

    /**
     * [发送手机或邮箱验证码]
     * @AuthorHTL
     * @DateTime  2017-09-18T14:16:45+0800
     * @param     [type]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
        $result = kernel::single('topmanageapi_security')->send($params);
        return  $result;
    }
}


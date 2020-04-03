<?php
/**
 * topapi
 *
 * -- addFeedback
 * -- 发送验证码
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_addFeedback implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家app意见反馈';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'name' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'张三', 'description'=>'提交意见的姓名'],
            'email' => ['type'=>'string', 'valid'=>'required|email', 'default'=>'', 'example'=>'example@shopex.cn', 'description'=>'平台处理后告知结果的邮箱'],
            'tel' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'021-68100100', 'description'=>'联系的电话或者手机号码'],
            'question' => ['type'=>'string','valid'=>'required|min:10|max:300', 'default'=>'', 'example'=>'', 'description'=>'反馈问题的详细描述']
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
        $objMdlFeedback = app::get('sysrate')->model('feedback');

        $postParams['shop_id']  = $params['oAuthInfo']['shop_id'];
        $postParams['seller_id']  = $params['oAuthInfo']['seller_id'];
        $postParams['name']     = $params['name'];
        $postParams['email']    = $params['email'];
        $postParams['tel']      = $params['tel'];
        $postParams['question'] = htmlspecialchars($params['question']);
        try
        {
            //检查数据安全
            $data = utils::_filter_input($postParams);

            $objMdlFeedback->save($data);
        }
        catch (Exception $e)
        {
            $msg = $e->getMessage();
            return ['status'=>'error','msg'=>$msg];
        }

        return ['status'=>'success','msg'=>'添加成功'];
    }
}


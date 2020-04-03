
<?php
/**
 * topmanageapi
 *
 * -- customer.consult.reply
 * -- 咨询回复
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_consultReply implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '咨询回复';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'id'  => ['type'=>'int', 'valid'=>'required' , 'example'=>'1','desc'=>'回复的咨询id'],
            'author_id' => ['type'=>'int','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人ID'],
            'author' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'', 'description'=>'咨询人'],
            'content'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'','desc'=>'回复的内容']
        ];
    }

    /**
     * @return array 咨询回复
     */
    public function handle($params)
    {
        $postParams['shop_id']      = $params['oAuthInfo']['shop_id'];
        $postParams['id']           = intval($params['id']);
        $postParams['author_id']    = intval($params['author_id']);
        $postParams['author']       = $params['author'];
        $postParams['content']      = htmlspecialchars($params['content']);

        $result = kernel::single('topmanageapi_customer_consult')->doReply($postParams);

        return $result;
    }

}
<?php
/**
 * topapi
 *
 * -- article.get
 * -- 获取文章详细内容
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_get implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取文章详细内容';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'id'     => ['type'=>'integer', 'valid'=>'required','desc'=>'文章id']
        ];
    }

    public function handle($params)
    {
        try {
            $postData['shop_id'] = $params['oAuthInfo']['shop_id'];
            $postData['article_id'] = (int)trim($params['id']);
            $postData['fields']  = '*';
            $result = app::get('topshop')->rpcCall('syscontent.shop.info.article', $postData);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','msg'=>$msg];
        }
        return $result;
    }
}


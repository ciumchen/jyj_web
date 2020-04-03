<?php
/**
 * topapi
 *
 * -- article.getnodes
 * -- 获取验证页数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_getNodes implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取商家文章分类';

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
 
    public function handle($params)
    {
        $postData['shop_id']= $params['oAuthInfo']['shop_id'];
        $postData['fields'] = 'node_id,node_name,order_sort,modified';
        $postData['order_by'] = 'node_id desc';
        $nodes = app::get('topshop')->rpcCall('syscontent.shop.list.article.node',$postData);
        return $nodes;
    }
}


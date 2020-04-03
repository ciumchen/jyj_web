<?php
/**
 * topapi
 *
 * -- article.list
 * -- 获取验证页数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_list implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '获取商家文章列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'page_size'    => ['type'=>'integer', 'valid'=>'', 'example'=>'10', 'desc'=>'每页显示商品数', 'msg'=>''],
            'pages_no'     => ['type'=>'integer', 'valid'=>'','desc'=>'页数'],
            's_k'          => ['type'=>'string', 'valid'=>'','desc'=>'文章名称(搜索使用)'],
            's_n'          => ['type'=>'string', 'valid'=>'','desc'=>'文章分类(搜索使用)']
        ];
    }
 
    public function handle($params)
    {
        $postData['shop_id']= $params['oAuthInfo']['shop_id'];
        $postData['pages_no'] =  $params['pages_no'] ? $params['pages_no']:'1';
        $postData['page_size'] =  $params['page_size'] ? $params['page_size']:'10';
        $postData['fields'] = 'article_id,title,platform,node_id,pubtime,modified';
        if ($params['s_k']) {
            $postData['keyword'] = specialutils::removeXSS($params['s_k']);
        }
        if ($params['s_n']) {
            $postData['node_id'] = $params['s_n'];
        }
        $result = app::get('topshop')->rpcCall('syscontent.shop.list.article', $postData);
        return $result;
    }
}


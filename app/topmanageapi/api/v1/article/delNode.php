<?php
/**
 * topapi
 *
 * -- article.delnode
 * -- 删除分类
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_delNode implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '删除文章分类';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'node_id'           => ['type'=>'int', 'valid'=>'required','desc'=>'分类id','msg'=>'分类id不能为空']
        ];
    }
 
    public function handle($params)
    {
        $postData['shop_id']    = $params['oAuthInfo']['shop_id'];
        $postData['node_id']    = $params['node_id'];
        try {
            $result = app::get('topshop')->rpcCall('syscontent.shop.del.article.node', $postData);
            if (!$result) {
                return ['status'=>'error','msg'=>'删除失败'];
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','msg'=>$msg];
        }
        return ['status'=>'success','msg'=>'删除成功'];
    }
}


<?php
/**
 * topapi
 *
 * -- article.savenode
 * -- 添加分类
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_saveNode implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '添加文章分类';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'node_id'           => ['type'=>'int', 'valid'=>'','desc'=>'分类id','msg'=>'分类id不能为空'],
            'node_name'         => ['type'=>'string', 'valid'=>'required|min:2|max:20','desc'=>'分类名称','msg'=>'分类名称不能为空|分类名称至少两个字|分类名称最多二十个字'],
            'order_sort'        => ['type'=>'int', 'valid'=>'required','desc'=>'分类排序','msg'=>'分类排序必填']
        ];
    }
 
    public function handle($params)
    {
        $postData['node_id']    = $params['node_id'] ? $params['node_id'] : '';
        $postData['node_name']  = $params['node_name'];
        $postData['order_sort'] = $params['order_sort'];
        $postData = specialutils::filterInput($postData);
        $postData['shop_id']    = $params['oAuthInfo']['shop_id'];
        try {
            $result = app::get('topshop')->rpcCall('syscontent.shop.save.article.node', $postData);
            if(!$result){
                return ['status'=>'error','msg'=>'保存失败'];
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','msg'=>$msg];
        }
        return ['status'=>'success','msg'=>'保存成功'];
    }

    private function __checkOrdersort($order_sort)
    {
        if(intval($order_sort) != $order_sort  || $order_sort < 0)
        {
            return ['status'=>'error','msg'=>'分类排序为正整数'];
        }

        return true;
    }
}


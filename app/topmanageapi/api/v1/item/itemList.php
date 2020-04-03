<?php
/**
 * topapi
 *
 * -- item.list
 * -- 商品搜索
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_itemList implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商品搜索';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'use_platform'  => ['type'=>'integer', 'valid'=>'' , 'example'=>'1','desc'=>'发布终端,pc端和wap端(0), pc端(1),wap端(2),不填则为-1'],
            'item_title'    => ['type'=>'string', 'valid'=>'', 'desc'=>'商品名称'],
            'item_no'       => ['type'=>'string','valid'=>'', 'example'=>'','desc'=>'商品货号'],
            'item_cat'      => ['type'=>'integer','valid'=>'','example'=>'','desc'=>'商品分类'],
            'min_price'     => ['type'=>'numeric','valid'=>'','example'=>'','desc'=>'最小价格'],
            'max_price'     => ['type'=>'numeric','valid'=>'','example'=>'','desc'=>'最大价格'],
            'dlytmpl_id'    => ['type'=>'integer','valid'=>'','example'=>'','desc'=>'运费模板id'],
            'page_size'     => ['type'=>'numeric', 'valid'=>'', 'example'=>'10', 'desc'=>'每页显示商品数', 'msg'=>''],
            'pages_no'      => ['type'=>'numeric', 'valid'=>'','desc'=>'页数'],
            'status'        => ['type'=>'string','valid'=>'in:onsale,instock,oversku','description'=>'商品状态'],
            'size'          => ['type'=>'string','valid'=>'','description'=>'图片默认大小默认为空L:大图，M:中图 , S:小图 ,T:微图']
        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        if($params['min_price']&&$params['max_price'])
        {
            if($params['min_price']>$params['max_price'])
             {
                $msg = app::get('topshop')->_('最大值不能小于最小值！');
                throw new \LogicException(app::get('topmanageapi')->_($msg));
            }
        }
        //获取商品列表
        $shop_id = $params['oAuthInfo']['shop_id'];
        $requestParams['shop_id'] = $shop_id;
        $requestParams['page_no'] =  $params['pages_no'] ? intval($params['pages_no']):'1';
        $requestParams['page_size'] =  $params['page_size'] ? intval($params['page_size']):'10';
        $requestParams['size'] = $params['size'] ? $params['size'] : '';
        $status = $params['status'] ? $params['status'] : '';
        if($params['item_title'])
        {
            $requestParams['search_keywords'] = $params['item_title'];
        }
        if($params['size'])
        {
            $requestParams['size'] = $params['size'];
        }        
        if($params['min_price'] && $params['max_price'])
        {
            $requestParams['min_price'] = $params['min_price'];
            $requestParams['max_price'] = $params['max_price'];
        }
        if($params['use_platform'] >= 0)
        {
            $requestParams['use_platform'] = $params['use_platform'];
        }
        if($params['item_cat'] && $params['item_cat'] > 0)
        {
            $requestParams['search_shop_cat_id'] = (int)$params['item_cat'];
        }
        if($params['item_no'])
        {
            $requestParams['bn'] = $params['item_no'];
        }
        if($params['dlytmpl_id']&&$params['dlytmpl_id']>0)
        {
            $requestParams['dlytmpl_id'] = $params['dlytmpl_id'];
        }
        if (!empty($status)) {
            $requestParams['approve_status'] = $status;
        }
        $requestParams['fields'] = 'item_id,list_time,modified_time,title,image_default_id,approve_status,price,store,dlytmpl_id,nospec,use_platform,cat_id';
        $requestParams['orderBy'] = 'modified_time desc';
        $result = kernel::single('topmanageapi_item_itemListinfo')->getItemlist($shop_id,$requestParams,$status);
        return  $result;
    }

}


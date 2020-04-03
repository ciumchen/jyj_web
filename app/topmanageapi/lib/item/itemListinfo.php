<?php
/**
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topmanageapi_item_itemListinfo {

    /**
     * 获取商品
     *
     * @param int $shop_id 店铺Id
     * @param string $page_no 当前页数
     * @param string $page_size 每页显示商品数
     *
     * @return string $itemList
     */
    public function getItemlist($shop_id,$filter,$status)
    {
        $page_no =  $filter['page_no'];
        $page_size =  $filter['page_size'];
        $size =  $filter['size'];
        //库存报警的商品列表
        if ($status == 'oversku') 
        {
            $params['shop_id'] = $shop_id;
            $params['fields'] = 'policevalue';
            $storePolice = app::get('topshop')->rpcCall('item.store.info',$params);
            $filter['store'] = $storePolice['policevalue']?$storePolice['policevalue']:0;
            $itemsList = app::get('topshop')->rpcCall('item.store.police',$filter);
        }else{
            $itemsList = app::get('topshop')->rpcCall('item.search',$filter);
        }
        //分页
        
        //格式化图片地址
        foreach ($itemsList['list'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k ==  'image_default_id') {
                    $value[$k] = base_storager::modifier($v,$size);
                }
            }
            $itemsList['list'][$key] = $value;
        }
        $result['item_list'] = $itemsList['list'];
        $result['total'] = $itemsList['total_found'];
        $result['totalPage'] = ceil($itemsList['total_found']/$page_size);
        $result['pager_no'] = $page_no;
        return $result;
    }

    /**
     * @AuthorHTL
     *
     * 获取店铺快递模板
     * 
     * @DateTime  2017-08-01T17:39:19+0800
     * @param     int $shop_id 店铺Id
     * @return    string $dlyList 快递模板信息
     */
    public function getDlytlist($shop_id)
    {
        $tmpParams = array(
            'shop_id' => $shop_id,
            'status' => 'on',
            'fields' => 'shop_id,name,template_id',
        );
        return $dlyList = app::get('topshop')->rpcCall('logistics.dlytmpl.get.list',$tmpParams);
    }

    /**
     * @AuthorHTL
     * 
     * 获取店铺分类
     * 
     * @DateTime  2017-08-01T17:42:19+0800
     * @param     int $shop_id 店铺id
     * @return    string $itemCatlist 分类信息
     */
    public function getItemcatList($shop_id)
    {
        $params['shop_id'] = $shop_id;
        return $itemCatlist = app::get('topshop')->rpcCall('shop.cat.get', $params);
    }
}


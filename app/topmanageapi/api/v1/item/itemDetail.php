<?php
/**
 * topapi
 *
 * -- item.detail
 * -- 商品详情
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_item_itemDetail implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商品详情';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'item_id'    => ['type'=>'integer', 'valid'=>'required|integer|min:1', 'example'=>'1', 'desc'=>'商品id，必须为正整数', 'msg'=>''],
            'size'          => ['type'=>'string','valid'=>'','description'=>'图片默认大小默认为空L:大图，M:中图 , S:小图 ,T:微图']
        ];
    }

    /**
     * @return array 商品详情
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        $item_id = intval($params['item_id']);
        $size = intval($params['size']);
        $itemparams['item_id'] = $item_id;
        $itemparams['shop_id'] = $shop_id;
        $itemparams['fields']  = "*,sku,item_store,item_status,item_count,item_desc,item_nature,spec_index";
        $result['item'] = app::get('topshop')->rpcCall('item.get',$itemparams);
        $sku = $this->getProducts($item_id);
        if ($result['item']['sku'])
        {
            unset($result['item']['sku']);
            $result['item']['sku'] = $sku;
        }
        //格式化图片地址
        foreach ($result['item']['images'] as $key => $value) 
        {
            $result['item']['images'][$key] = base_storager::modifier($value,$size);
        }
        // 商家分类及此商品关联的分类
        $scparams['shop_id'] = $shop_id;
        $scparams['fields'] = 'cat_id,cat_name,is_leaf,parent_id,level';
        $result['shopCatList'] = app::get('topshop')->rpcCall('shop.cat.get',$scparams);
        $objMdlProps = app::get('syscategory')->model('props');
        if( $result['item']['spec_desc'] && is_array( $result['item']['spec_desc'] ) )
        {
            $propIds = implode(',',array_keys($result['item']['spec_desc']));
            $propsList = app::get('topshop')->rpcCall('category.prop.list',array('prop_id'=>$propIds));
            foreach( $result['item']['spec_desc'] as $specId => $spec )
            {
                $result['item']['spec'][$specId] = $propsList[$specId];
                foreach( $spec as $pSpecId => $specValue )
                {
                    $result['item']['spec'][$specId]['option'][$pSpecId] = $specValue ;
                }
            }
        }
        unset($result['item']['spec_desc']);
        $result['dlytmpls'] = kernel::single('topmanageapi_item_itemListinfo')->getDlytlist($shop_id);
        /*foreach ($result['item']['spec_desc'] as $key => $value) {
                $option ['option'] =  $value;
                 $result['item']['spec_desc'][$key]= $option;
        }*/
        return $result;
    }

    /**
     * 获取商品的sku
     */
    public function getProducts($item_id)
    {
        $productData = app::get('topshop')->rpcCall('item.sku.list',array('item_id'=>$item_id));
        foreach((array)$productData as $row)
        {
            $unique_id = $this->get_unique_id($row['spec_desc']['spec_value_id']);
            $row['freez'] = ($row['freez'] !== null) ? $row['freez'] : '0';
            $returnData[$unique_id] = $row;
        }

         return  $returnData;
    }

        /*
     * 每个货品的唯一键值(根据每个货品的规格ID生成) 在js中需要此键值来加载对应的数据
     * */
    private function get_unique_id($spec){
        $str = implode(';',$spec);
        return substr(md5($str),0,10);
    }
}
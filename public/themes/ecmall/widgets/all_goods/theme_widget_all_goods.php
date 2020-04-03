<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_all_goods(&$setting){
    $rows = 'item_id,title,price,image_default_id,sold_quantity,modified_time';
    $objItem = kernel::single('sysitem_item_info');
    $setting['item'] = $objItem->getItemList($setting['item_select'], $rows);
    sort($setting['item']);
    // 根据配置参数控制前台显示数量
    $setting['item'] = array_slice($setting['item'], 0, $setting['limit']);
    $setting['defaultImg'] = app::get('image')->getConf('image.set');
    if ($userId = userAuth::id())
    {
        $collect = app::get('topc')->rpcCall('user.collect.info',array('user_id'=>$userId));
        foreach ($setting['item'] as $key =>$value)
        {
            if (in_array($value['item_id'], $collect['item'])) {
                $setting['item'][$key]['collect'] = 1;
            }else{
                $setting['item'][$key]['collect'] = 0;
            }
        }
    }
    return $setting;
}
?>

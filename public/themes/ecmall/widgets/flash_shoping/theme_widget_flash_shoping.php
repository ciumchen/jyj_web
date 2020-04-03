<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_flash_shoping(&$setting){
    $activity_id = $setting['activity_select'][0];
    $params = [
                'activity_id' => $activity_id,
                'fields'     => 'activity_id,activity_name,activity_tag,start_time,end_time',
        ];
    $activity = app::get('topc')->rpcCall('promotion.activity.list', $params);
    if ($activity['end_time'] < time() || $activity['start_time'] > time()) {
        $activity['status'] = 0;
    }
    $setting['activity']      = $activity['data'][0];
    $setting['activity_item'] = app::get('topc')->rpcCall('promotion.activity.item.list',['activity_id'=>$activity_id]);
    $setting['time'] = time();
    return $setting;
}
?>

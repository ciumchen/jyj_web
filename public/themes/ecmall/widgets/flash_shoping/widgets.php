<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*基础配置项*/
$setting['author']='shopex';
$setting['version']='v1.0';
$setting['name']='首页闪购挂件';
$setting['order']=0;
$setting['stime']='2018-07';
$setting['catalog']='商品相关';
$setting['description'] = '首页闪购挂件';
$setting['userinfo'] = '';
$setting['usual']    = '1';
$setting['tag']    = 'auto';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$setting['limit']    = '3';

    function getActivityList()
    {
        $params = [
                'start_time' => 'sthan',
                'end_time'   => 'bthan',
                'fields'     => 'activity_id,activity_name,activity_tag,start_time,end_time',
        ];
        $activityList = app::get('topc')->rpcCall('promotion.activity.list', $params);
        return $activityList;
    }
$setting['activityList'] = getActivityList();
$setting['time']    = time();
?>

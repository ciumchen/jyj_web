<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_wap_ad_red_packet1(&$setting){
    if( $setting['hongbao_list'] )
    {
        $i = 0;
        foreach( $setting['hongbao_list'] as $hongbaoId => $row )
        {
            foreach($row as $money)
            {
                // if( $i >= 4 ) break;
                $data[$i]['name'] = $setting['hongbao_name'][$hongbaoId];
                $data[$i]['hongbao_id'] = $hongbaoId;
                $data[$i]['money'] = $money;
                $i++;
            }
        }

        $setting['hongbao'] = $data;
    }
    return $setting;
}
?>

<?php

class sysaftersales_setting {

    public function getAftersalesSettingByCatId($cat_id = 0)
    {
        $refundSetting = unserialize(app::get('sysaftersales')->getConf('refundSetting'));
        $changingSetting = unserialize(app::get('sysaftersales')->getConf('changingSetting'));
        $objMdlAftersales = app::get('sysaftersales')->model('setting');
        if($cat_id > 0)
            $catData = $objMdlAftersales->getRow('*', ['cat_id'=>$cat_id]);
        else
            $cateData = false;

        $return = array(
            'cat_id'=> $cat_id,
            'changing_active' => $changingSetting['status'],
            'refund_active' => $refundSetting['status'],
        );

        if($refundSetting['status'])
        {
            if($catData && $catData['refund_days'] >= 0){
                if($catData['refund_days'] == 0)
                {
                    $return['refund_active'] = null;
                }else{
                    $return['refund_days'] = $catData['refund_days'];
                }
            }else{
                if($refundSetting['day'] == 0)
                {
                    $return['refund_active'] = null;
                }else{
                    $return['refund_days'] = $refundSetting['day'];
                }
            }
        }

        if($changingSetting['status'])
        {
            if($catData && $catData['changing_days'] >= 0){
                $return['changing_days'] = $catData['changing_days'];
            }else{
                $return['changing_days'] = $changingSetting['day'];
            }
        }

        return $return;
    }

}


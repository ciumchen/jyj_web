<?php
class topmanageapi_customer_afterSales{

/**
 * [__searchListData 获取退货单列表]
 * @AuthorHTL
 * @DateTime  2017-10-24T18:00:27+0800
 * @param     [type]                   $params [description]
 * @return    [array]                           [列表信息]
 */
    public function __searchListData($params)
    {
        try {
            $result = app::get('topshop')->rpcCall('aftersales.list.get', $params);
            $result['list'] = $this->__proResult($result);
            foreach ($result['list'] as $key => $value)
            {
                foreach ($value['sku'] as $k => $v)
                {
                    if ($k ==  'pic_path')
                    {
                        //默认小图
                        $value['sku'][$k] = base_storager::modifier($v,$params['size']);
                    }
                }
                $result['list'][$key] = $value;
            }
        } catch (Exception $e) {
            $result = array();
        }

        $result['refund_type'] = array(
            'ONLY_REFUND' => app::get('topshop')->_('仅退款'),
            'REFUND_GOODS' => app::get('topshop')->_('退货退款'),
            'EXCHANGING_GOODS' => app::get('topshop')->_('换货'),
        );
        $result['progress'] = array(
            '0' => app::get('topshop')->_('等待审核'),
            '1' => app::get('topshop')->_('等待买家回寄'),
            '2' => app::get('topshop')->_('待确认收货'),
            '5' => app::get('topshop')->_('商家已收货'),
            '8' => app::get('topshop')->_('等待平台退款'),
            '3-4-6-7' => app::get('topshop')->_('已完成'),//换货的时候可以直接在商家处理结束
        );

        $result['complaints'] = array(
                'NOT_COMPLAINTS' => app::get('topshop')->_('未投诉'),
                'WAIT_SYS_AGREE' => app::get('topshop')->_('买家已发起投诉'),
                'FINISHED' => app::get('topshop')->_('投诉受理'),
                'BUYER_CLOSED' => app::get('topshop')->_('买家撤销投诉'),
                'CLOSED' => app::get('topshop')->_('投诉驳回'),
        );
        return $result;
    }

    /**
     * [__getDataInfo 获取售后订单详情]
     * @param  [type] $shop_id       [description]
     * @param  [type] $aftersales_bn [description]
     * @return [type]                [description]
     */
    public function __getDataInfo($shop_id,$aftersales_bn)
    {

        $requestParams = ['shop_id' => $shop_id];
        $shopConf = app::get('topshop')->rpcCall('open.shop.develop.conf', $requestParams);
        $postParams['aftersales_bn'] = $aftersales_bn;
        $postParams['shop_id'] = $shop_id;
        $tradeFields = 'trade.status,trade.receiver_name,trade.user_id,trade.post_fee,trade.receiver_state,trade.receiver_city,trade.created_time,trade.receiver_district,trade.receiver_address,trade.receiver_mobile,trade.receiver_phone';
        $postParams['fields'] = 'aftersales_bn,shop_id,aftersales_type,sendback_data,description,sendconfirm_data,shop_explanation,admin_explanation,user_id,reason,evidence_pic,created_time,oid,tid,num,progress,status,sku,refunds_reason,gift_data,'.$tradeFields;
        try {
            $result = app::get('topshop')->rpcCall('aftersales.get', $postParams);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','message'=>$msg];
        }
        $result['evidence_pic'] = $result['evidence_pic'] ? explode(',',$data['evidence_pic']) : null;
        $result['sendback_data'] = $result['sendback_data'] ? unserialize($data['sendback_data']) : null;
        $result['sendconfirm_data'] = $result['sendconfirm_data'] ? unserialize($data['sendconfirm_data']) : null;

        if( $result['user_id'] )
        {
             $userName = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => $result['user_id']]);
             $result['userName'] = $userName[$result['user_id']];
        }

        if ($result['shop_id']) {
            $shopinfo = app::get('topshop')->rpcCall('shop.get.detail',['shop_id'=>$result['shop_id']]);
            $result['shopName'] = $shopinfo['shop']['shop_name'];
        }
        if( $result['sendback_data']['corp_code']  && $result['sendback_data']['corp_code'] != "other")
        {
            $logiData = explode('-',$result['sendback_data']['corp_code']);
            $result['sendback_data']['corp_code'] = $logiData[0];
            $result['sendback_data']['logi_name'] = $logiData[1];
        }

        if( $result['sendconfirm_data']['corp_code'] && $result['sendconfirm_data']['corp_code'] != "other")
        {
            $logiData = explode('-',$result['sendconfirm_data']['corp_code']);
            $result['sendconfirm_data']['corp_code'] = $logiData[0];
            $result['sendconfirm_data']['logi_name'] = $logiData[1];
        }

        //快递公司代码
        $corpData = app::get('topshop')->rpcCall('shop.dlycorp.getlist',['shop_id'=>$this->shopId]);
        $data['corpData'] = $corpData['list'];
        if ($result['gift_data'])
        {
            foreach ($result['gift_data'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if ($k == 'image_default_id')
                        {
                            $value[$k] = base_storager::modifier($v,'');
                        }
                    }
                }
                $result['gift_data'][$key] = $value;
            }
        }
        $data['info'] = $result;

        $data['defaultImageId'] = kernel::single('image_data_image')->getImageSetting('item');

        //商家退款信息
        if(in_array($result['progress'],['7','8']))
        {
            $refunds = app::get('topshop')->rpcCall('aftersales.refundapply.list.get',['fields'=>'status,total_price','oid'=>$result['oid']]);
            $refunds = $refunds['list'][0];
            $data['refunds'] = $refunds;
        }

        $data['tracking'] = app::get('syslogistics')->getConf('syslogistics.order.tracking');

        $data['develop_mode'] = ($shopConf['develop_mode'] == 'DEVELOP' || $shopConf['shopexProduct'] == 'bind' ) ? 'DEVELOP' : 'PRODUCT';
        return $data;

    }


/**
 * [verification 处理售后订单]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function verification($params)
    {
        try {
            $result = app::get('topshop')->rpcCall('aftersales.check',$params);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','message'=>$msg];
        }
        return ['status'=>'success','message'=>'操作成功'];
    }

/**
 * [sendConfirm 售后处理 换货 填写订单信息]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function sendConfirm($params)
    {
        try {
            $filter['aftersales_bn'] = $params['aftersales_bn'];
            $filter['shop_id'] = $params['shop_id'];

            $data['corp_code'] = $params['corp_code'];
            $data['logi_name'] = $params['logi_name'];
            $data['logi_no'] = $params['logi_no'];
            $type = 'seller_confirm';
            $result = kernel::single('sysaftersales_progress')->sendGoods($filter,$type, $data,$params['shop_id']);
            $this->__event($result, $data);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            return ['status'=>'error','message'=>$msg];
        }
        $msg = '操作成功';
        return ['status'=>'success','message'=>$msg];
    }



        /**
     * 处理返回数据
     * @param array $result
     *
     * @return array
     * */

    private function __proResult($result)
    {
        $oids = array_column($result['list'], 'oid');
        $oids= array_unique($oids);
        $tmpList = array();

        foreach ($oids as $ov)
        {
            foreach ($result['list'] as $val)
            {
                if($ov == $val['oid'])
                {
                    $tmpList[$ov][] = $val;
                }
            }
        }

        // 添加投诉状态
        foreach ($tmpList as &$tval)
        {
            if(count($tval) <= 1)
            {
                continue;
            }

            foreach ($tval as $k=>$v)
            {
                if($k!=0 && $v['progress'] == 3)
                {
                    $tval[$k]['complaints_finished'] = 1;
                }
            }

            /*
            if($tval[0]['progress'] == 3)
            {
                if($tval[0]['sku']['complaints_status'] == 'NOT_COMPLAINTS')
                {
                    $tval[0]['arge'] = 1;
                }
            }
            */
        }

        // 取得结果
        $proList = array();
        foreach ($tmpList as $vv)
        {
            $proList = array_merge($proList, $vv);
        }

        // 根据售后发起时间逆向排序
        $tmpResultList = array();
        foreach ($proList as $pval)
        {
            $tmpResultList[$pval['created_time']] = $pval;
        }
        krsort($tmpResultList);

        return $tmpResultList;
    }

        private function __event($result, $data)
        {
            $eventData['aftersales_bn'] = $result['aftersales_bn'];
            $eventData['corp_code'] = $data['corp_code'];
            $eventData['logi_name'] = $data['logi_name'];
            $eventData['logi_no'] = $data['logi_no'];
            $eventData['tid'] = $result['tid'];
            $eventData['oid'] = $result['oid'];
            $eventData['user_id'] = $result['user_id'];

            event::fire('aftersales.sellerSendGoods', [$eventData, $result['shop_id']]);

            return true;
        }

}
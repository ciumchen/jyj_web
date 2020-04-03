<?php
/**
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topmanageapi_trade_tradeListinfo {

    /**
     * 获取商品
     *
     * @param int $shop_id 店铺Id
     * @param string $page_no 当前页数
     * @param string $page_size 每页显示商品数
     * @param array $postFilter 
     *
     * @return array $itemList
     */
    public function getTradelist($shop_id,$limit = 10,$page = 1,$filter)
    {
        $tradeStatus = array(
            'WAIT_BUYER_PAY' => '未付款',
            'WAIT_SELLER_SEND_GOODS' => '已付款，请发货',
            'WAIT_BUYER_CONFIRM_GOODS' => '已发货，等待确认',
            'TRADE_FINISHED' => '已完成',
            'TRADE_CLOSED' => '已关闭',
            'TRADE_CLOSED_BY_SYSTEM' => '已关闭',
        );

        $status = $filter['status'];
        if(is_array($filter['status']))
        {
            $status = implode(',',$filter['status']);
        }
        $postData = array(
            'status' => $status,
            'tid' => $filter['tid'],
            'create_time_start' =>$filter['created_time_start'],
            'create_time_end' =>$filter['created_time_end'],
            'receiver_mobile' =>$filter['receiver_mobile'],
            'receiver_phone' =>$filter['receiver_phone'],
            'receiver_name' =>$filter['receiver_name'],
            'user_name' =>$filter['user_name'],
            'pay_type' =>$filter['pay_type'],
            'shipping_type' =>$filter['shipping_type'],
            'settlement_status' =>$filter['settlement_status'],
            'page_no' => intval($page),
            'page_size' =>intval($limit),
            'order_by' =>'created_time desc',
            'fields' =>'order.spec_nature_info,shipping_type,tid,shop_id,user_id,status,settlement_status,payment,points_fee,total_fee,post_fee,payed_fee,receiver_name,trade_memo,created_time,receiver_mobile,discount_fee,adjust_fee,order.title,order.price,order.num,order.pic_path,order.tid,order.oid,order.item_id,need_invoice,invoice_name,invoice_type,invoice_main,pay_type,cancel_status,order.gift_data',
        );
        $postData['is_aftersale'] = true;
        $postData['shop_id'] = $shop_id;
        $tradeList = app::get('topshop')->rpcCall('trade.get.list',$postData);
        $count = $tradeList['count'];
        $tradeList = $tradeList['list'];
        $usersId = array_column($tradeList, 'user_id');
        //获取用户名
        if( $usersId )
        {
            $username = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => implode(',', $usersId)]);
        }
        foreach((array)$tradeList as $key=>$value)
        {
            $tid[] = $value['tid'];
            $tradeList[$key]['status_depict'] = $tradeStatus[$value['status']];
            $tradeList[$key]['user_login_name'] = $username[$value['user_id']];
        }
        $tradeList = $this->__removeKey($tradeList);
        $result['orderlist'] = $this->__check($tradeList); 
        $result['count'] = $count;
        return $result;
    }


/**
 * 返回数据优化
 * @AuthorHTL
 * @DateTime  2017-09-12T13:23:18+0800
 * @param     [array]                   $array [description]
 * @return    [array]                          [description]
 */
    private function __removeKey($array)
    {
        $fmtArray = [];
        foreach($array as $key=>$value)
        {
            if(is_array($value[$value['tid']]))
            {
                $value[$value['tid']] = $this->__removeKey($value[$value['tid']]);
            }
            $fmtArray[] = $value;
        }
        return $fmtArray;
    }

/**
 * 订单数据中价格和时间转换
 * @AuthorHTL
 * @DateTime  2017-09-12T16:25:44+0800
 * @param     [type]                   $array [description]
 * @return    [type]                          [description]
 */
    private function __check($array)
    {
        $aaa = [];
        foreach ($array as $key => $value) 
        {
            if (is_array($value)) 
            {
                foreach ($value as $k => $v) 
                {
                    if ($k == 'created_time') 
                    {
                        $value[$k] = date("Y-m-d",$v);
                    }
                    if ($k == 'discount_fee' || $k == 'payment' || $k == 'points_fee' || $k == 'total_fee' || $k == 'payed_fee' || $k == 'price') {
                        $value[$k] = number_format($v,2);
                    }
                    if ($k == 'order' && $v) 
                    {
                        $bbb = [];
                        foreach ($v as $ke => $va) 
                        {
                           if (is_array($va)) {
                               foreach ($va as $a => $b) {
                                   if ($a == 'price')
                                   {
                                       $va[$a] = number_format($b,2);
                                   }
                                   if ($a == 'pic_path')
                                   {
                                        $va[$a] = base_storager::modifier($b,'');
                                   }
                                   if ($a == 'gift_data')
                                   {
                                        if (is_array($b))
                                        {
                                            $eee = [];
                                            foreach ($b as $f)
                                            {
                                                foreach ($f as $h=> $j)
                                                {
                                                    if ($h == 'image_default_id')
                                                    {
                                                        $f[$h] = base_storager::modifier($j,'');
                                                    }
                                                }
                                                $eee[] = $f;
                                            }
                                        }
                                        $va[$a] = $eee;
                                   }
                               }
                           }
                           $bbb[] = $va;
                        }
                        $value[$k] = $bbb;
                    }
                }
            }
            $aaa[] = $value;
        }
        return $aaa;
    }


/**
 * 订单详情里的价格时间格式优化
 * @AuthorHTL
 * @DateTime  2017-09-13T11:59:49+0800
 * @param     [type]                   $array [description]
 * @return    [type]                          [description]
 */
    public function __checkTradeinfoParams($array)
    {
        foreach ($array as $key => $value)
        {
            $keyList =['payment','points_fee','payed_fee','total_fee','discount_fee','adjust_fee'];
            if (in_array($key, $keyList))
            {
                $array[$key] = number_format($value,2);
            }
            if ($key == 'orders')
            {
                if(is_array($value))
                {
                    $dd = [];
                    foreach ($value as $ke => $va) {
                        $priceKey = ['price','total_fee','adjust_fee','refund_fee'];
                     if (is_array($va)) {
                         foreach ($va as $k => $v)
                         {
                             if (in_array($k,$priceKey)) {
                                 $va[$k] = number_format($v,2);
                             }
                             if ($k == 'pic_path')
                             {
                                $va[$k] = base_storager::modifier($v,'');
                             }
                            if ($k == 'gift_data')
                            {
                                if (is_array($v))
                                {
                                    $eee = [];
                                    foreach ($v as $f)
                                    {
                                        foreach ($f as $h=> $j)
                                        {
                                            if ($h == 'image_default_id')
                                            {
                                                $f[$h] = base_storager::modifier($j,'');
                                            }
                                        }
                                        $eee[] = $f;
                                    }
                                }
                                    $va[$k] = $eee;
                            }
                         }
                         $dd[] = $va;
                     }
                    }
                $array[$key] = $dd;
                }
            }
        }
        return $array;
    }
}


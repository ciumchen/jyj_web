<?php
class topmanageapi_getAverPrice{

    public function get($data,$shop_id)
    {
        switch($data)
        {
            case 'today':
                $stattime = strtotime(date("Y-m-d", time()) . ' 00:00:00');
                $filterType = "nequal";
                    break;
            case "yesterday":
                $stattime = strtotime(date("Y-m-d", time()-86400) . ' 00:00:00');
                $filterType = "nequal";
                break;
            case "beforday":
                $stattime = strtotime(date("Y-m-d", time()-86400*2) . ' 00:00:00');
                $filterType = "nequal";
                break;
            case "week":
                $stattime = strtotime(date("Y-m-d", time()-86400*7) . ' 00:00:00');
                $filterType = "bthan";
                break;
            case "month":
                $stattime = strtotime(date("Y-m-d", time()-86400*30) . ' 00:00:00');
                $filterType = "bthan";
                break;
        }
        $filter = array(
            'shop_id' => $shop_id,
            'type' => $filterType,
            'createtime' => $stattime,
        );
        $getData = app::get('topshop')->rpcCall('stat.trade.data.count.get',$filter);
        $data = array();
        foreach ($getData as $key => $value)
        {
            $data['shop_id'] =$value['shop_id'];
            $data['new_trade'] +=$value['new_trade'];
            $data['new_fee'] +=$value['new_fee'];
            $data['ready_trade'] +=$value['ready_trade'];
            $data['ready_fee'] +=$value['ready_fee'];
            $data['ready_send_trade'] +=$value['ready_send_trade'];
            $data['ready_send_fee'] +=$value['ready_send_fee'];
            $data['already_send_trade'] +=$value['already_send_trade'];
            $data['already_send_fee'] +=$value['already_send_fee'];
            $data['cancle_trade'] +=$value['cancle_trade'];
            $data['complete_trade'] +=$value['complete_trade'];
            $data['complete_fee'] +=$value['complete_fee'];
            $data['createtime'] =$value['createtime'];
        }

        if($data['new_trade']==0)
        {
            $data['averPrice'] = 0;
        }
        else
        {
            $data['averPrice'] = number_format($data['new_fee'] / $data['new_trade'], 2, '.',' ');
        }
        return $data;
    }

/**
 * 获取未付款的订单总金额
 * @AuthorHTL
 * @DateTime  2017-09-14T17:03:41+0800
 * @param     [type]                   $array [description]
 * @return    [type]                          [description]
 */
    public function tradeCount($array)
    {
        foreach ($array as $key => $value)
        {
            if (!$value) {
                unset($params[$key]);
            }
        }

        if($array['status'])
        {
            $array['status'] = explode(',',$array['status']);

        }
        $objMdlTrade = app::get('systrade')->model('trade');
        $count = $objMdlTrade->getList('payment',$array);
        foreach ($count as $key => $value) {
            $countPayment += $value['payment'];
        }
        return number_format($countPayment,2);
    }
}

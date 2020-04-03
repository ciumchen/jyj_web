<?php
/**
 * topapi
 *
 * -- trade.list
 * -- 交易列表
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_trade_list implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '交易列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            //'shop_id'  => ['type'=>'string', 'valid'=>'required', 'example'=>'1',   'desc'=>'商家id', 'msg'=>'shop_id不存在'],
            'page_size'         => ['type'=>'integer', 'valid'=>'', 'example'=>'10', 'desc'=>'每页显示商品数', 'msg'=>''],
            'pages_no'          => ['type'=>'integer', 'valid'=>'','desc'=>'页数'],
            'status'            => ['type'=>'int','valid'=>'', 'example'=>'1','description'=>'订单状态,0:全部,1:待支付,2:待发货,3:待收货,4:已收货,5;已取消,6:货到付款,7:自提订单'],
            'tid'               => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单编号,多个用逗号隔开'],
            'create_time'       => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'2017/09/11-2017/09/12','description'=>'查询指定时间内的交易创建时间yyyy/MM/dd - yyyy/MM/dd'],
            'receiver_mobile'   => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'收货人手机'],
            'receiver_name'     => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'收货人姓名'],
            'user_name'         => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'会员用户名/手机号/邮箱'],
            'is_aftersale'      => ['type'=>'bool', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'是否显示售后状态'],
            'settlement_status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'结算状态-1:：全部，1：普通结算，2：运费结算，3：售后结算，4：拒收结算']
            /*'pay_type' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'支付方式【offline、online】'],
            'shipping_type' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'配送类型']//目前用不到*/
        ];
    }

    /**
     * @return array 订单列表
     */
    public function handle($params)
    {
        //获取订单列表
        $shop_id = $params['oAuthInfo']['shop_id'];
        $limit = $params['page_size'] ? $params['page_size'] : 10;
        $page = $params['pages_no'] ? $params['pages_no'] :1;
        $postFilter['status']               = $params['status'];  //订单状态
        $postFilter['settlement_status']    = $params['settlement_status'];//结算状态
        $postFilter['tid']                  = $params['tid'];  //订单号
        $postFilter['create_time']          = $params['create_time'];  //订单创建时间
        $postFilter['receiver_mobile']      = $params['receiver_mobile'];  // 收货人手机号码
        //$postFilter['receiver_phone']       = $params['receiver_phone'];
        $postFilter['receiver_name']        = $params['receiver_name'];  //收货人
        $postFilter['user_name']            = $params['user_name'];  //会员账号
        $postFilter['settlement_status']    = $params['settlement_status']; //结算状态        
        //校验搜索数据
        $filter = $this->_checkParams($postFilter);
        $result = kernel::single('topmanageapi_trade_tradeListinfo')->getTradelist($shop_id,$limit,$page,$filter);
        return $result;
    }

    /**
 * 校验搜索数据
 * @AuthorHTL
 * @DateTime  2017-09-12T13:24:23+0800
 * @param     [array]                   $filter [description]
 * @return    [array]                           [description]
 */
    private function _checkParams($filter)
    {
        if($filter['settlement_status'] == '-1'){
            unset($filter['settlement_status']);
        }
        $statusLUT = array(
            '1' => 'WAIT_BUYER_PAY',
            '2' => 'WAIT_SELLER_SEND_GOODS',
            '3' => 'WAIT_BUYER_CONFIRM_GOODS',
            '4' => 'TRADE_FINISHED',
            '5' => array('TRADE_CLOSED','TRADE_CLOSED_BY_SYSTEM'),
        );
        foreach($filter as $key=>$value)
        {
            if(!$value) unset($filter[$key]);
            if($key == 'create_time')
            {
                $times = array_filter(explode('-',$value));
                if($times)
                {
                    $filter['created_time_start'] = strtotime($times['0']);
                    $filter['created_time_end'] = strtotime($times['1'])+86400;
                    unset($filter['create_time']);
                }
            }

            if($key=='status' && $value)
            {
                if($value <= 5)
                {
                    $filter['status'] = $statusLUT[$value];
                }
                else
                {
                    if($value == 6)
                    {
                        $filter['pay_type'] = 'offline';
                    }
                    if($value == 7)
                    {
                        $filter['shipping_type'] = 'ziti';
                    }
                    unset($filter['status']);
                }
            }
        }
        return $filter;
    }
}


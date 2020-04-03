<?php
/**
 * topapi
 *
 * -- statement.shop.trade.analyze
 * -- 交易数据分析
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_tradeAnalyze implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '交易数据分析';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month,select', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>''],
            'itemtime'   => ['type'=>'string','valid'=>'','example' => '','desc' =>' 起始时间段。如：2015/05/15-2015/05/15'],
            /*'endtime'   => ['type'=>'string','valid'=>'','example' => '','desc' =>' 结束时间段。如：2015/05/15-2015/05/15'],*/
            'compare' => ['type'=>'string','valid'=>'','example' => 'compare','desc' =>'只有在对比查询时需要的参数'],
            'page_size'     => ['type'=>'numeric', 'valid'=>'', 'example'=>'10', 'desc'=>'每页显示记录数', 'msg'=>''],
            'pages_no'      => ['type'=>'numeric', 'valid'=>'','desc'=>'页数']

        ];
    }

    /**
     * @return array sysstat 头部交易数据
     * @return string sysstat.new_fee 新增订单金额
     * @return string sysstat.new_trade 新增订单数量
     * @return string sysstat.averPrice 平均客单价
     * @return string sysstat.alreadyfee 已付款订单金额
     * @return string sysstat.cancle_fee 已取消订单金额
     * @return string sysstat.before.new_fee （对比）新增订单金额
     * @return string sysstat.after.new_fee （对比）对比金额
     * @return string sysstat.before.new_trade （对比）新增订单数量
     * @return string sysstat.after.new_trade （对比）对比订单数量
     * @return string sysstat.before.averPrice （对比）平均客单价
     * @return string sysstat.after.averPrice （对比）对比客单价
     * @return string sysstat.before.alreadyfee （对比）已付款订单金额
     * @return string sysstat.after.alreadyfee （对比）对比已付款订单金额
     * @return string sysstat.before.cancle_fee （对比）已取消订单金额
     * @return string sysstat.after.cancle_fee （对比）对比已取消订单金额
     * @return string compare 对比标签只有在对比时才有
     * @return string pages_no 当前页数
     * @return string page_size 每页显示数目
     * @return string count 总数
     * @return string sendtype 查询时间类型
     * @return string pagetime 页面显示时间
     * @return array sysTradeData   订单数据
     */
    public function handle($params)
    {
        $shopId = $params['oAuthInfo']['shop_id'];
        $type = $params['timetype'];
        $postSend['sendtype'] = $params['timetype'];
        if ($params['itemtime'])
        {
            $postSend['starttime'] = $params['itemtime'];
        }
       /* if ($params['endtime'])
        {
            $postSend['endtime'] = $params['endtime'];
        }*/
        if ($params['compare'])
        {
            $postSend['compare'] = $params['compare'];
        }
        $postSend['pages'] = $params['pages_no'] ? $params['pages_no'] :1;
        $result = kernel::single('topmanageapi_statement_tradeAnalyze')->get_tradeAnalyze_data($postSend,$type);
        return $result;
    }
}



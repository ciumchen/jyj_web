<?php
/**
 * topapi
 *
 * -- statement.shop.order.aftersale.analyze
 * -- 问题订单分析
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_orderAftersaleAnalyze implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '问题订单分析';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'      => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month,select', 'example'   =>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>''],
            'itemtime'     => ['type'=>'string','valid'=>'','example' => '','desc' =>' 起始时间段。如：2015/05/15-2015/05/15'],
            //app端取消对比时间
            /*'endtime'       => ['type'=>'string','valid'=>'','example' => '','desc' =>' 结束时间段。如：2015/05/15-2015/05/15'],*/
            'compare'       => ['type'=>'string','valid'=>'','example' => 'compare','desc' =>'只有在对比查询时需要的参数']
            /*'page_size'     => ['type'=>'numeric', 'valid'=>'', 'example'=>'10', 'desc'=>'每页显示记录数', 'msg'=>''],
            'pages_no'      => ['type'=>'numeric', 'valid'=>'','desc'=>'页数']*/

        ];
    }

    /**
     * @return array sysstat 头部交易数据
     * @return string sysstat.refund_trade 新增退货退款订单数
     * @return string sysstat.reject_trade 新增拒收订单数量
     * @return string sysstat.changing_trade 新增换货订单数量
     * @return string sysstat.total_refund_fee 退款总额
     * @return string sysstat.before.refund_trade 新增退货退款订单数
     * @return string sysstat.after.refund_trade （对比）新增退货退款订单数
     * @return string sysstat.before.reject_trade 新增拒收订单数量
     * @return string sysstat.after.reject_trade （对比）新增拒收订单数量
     * @return string sysstat.before.changing_trade 新增换货订单数量
     * @return string sysstat.after.changing_trade （对比）新增换货订单数量
     * @return string sysstat.before.total_refund_fee 退款总额
     * @return string sysstat.after.total_refund_fee （对比）退款总额
     * @return array refundToptenItem 退货前十的商品
     * @return array changingToptenItem 换货前十的商品
     * @return string sendtype 时间类型
     * @return string pagetime 页面时间
     * @return pagetime pagetime 页面时间
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
        $result = kernel::single('topmanageapi_statement_orderAftersaleAnalyze')->get_orderAftersale_data($postSend,$type);
        return $result;
    }

}



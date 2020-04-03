<?php
/**
 * topapi
 *
 * -- statement.shop.trade.analyze.chart
 * -- 交易数据分析图表数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_tradeAnalyzechart implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '交易数据分析图表数据';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month,select,selecttime', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>''],
            'tradeType' =>['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'报表ajax请求的数据类型'],
            'starttime' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'起始时间段。如：2015/05/15-2015/05/15'],
            'endtime' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'结束时间段。如：2015/05/03-2015/05/03'],
            'compare'  => ['type'=>'string','valid'=>'','default'=>'','example'=>'compare','desc'=>'对比查询时需要带上的参数，值为：compare']
        ];
    }

/**
 * [商家运营概况图表数据]
 * @AuthorHTL
 * @DateTime  2017-10-19T10:12:58+0800
 * @param     [array]                   $params [接口参数]
 * @return    [array]                   $data   [图表数据]
 */
    public function handle($params)
    {
        $postData['sendtype'] = $params['timetype'];
        $postData['trade'] = $params['tradeType'];
        if ($params['starttime']) {
            $postData['starttime'] = $params['starttime'];
        }
        if ($params['endtime']) {
            $postData['endtime'] = $params['endtime'];
        }
        if ($params['compare']) {
            $postData['compare'] = $params['compare'];
        }
        $result = kernel::single('topmanageapi_statement_operaInfo')->ajaxTrade($postData);
        return $result;
    }
}



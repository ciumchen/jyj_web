<?php
/**
 * topapi
 *
 * -- statement.shop.opera.info.chart
 * -- 商品销售分析图表数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_itemAnalyzechart implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商品销售分析图表数据';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month,selecttime', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>''],
            'tradeType' =>['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'报表ajax请求的数据类型'],
            'itemtime' => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询时间段。如：2015/05/03-2015/05/03'],
        ];
    }

    /**
     * [商品销售分析图表数据]
     * @AuthorHTL
     * @DateTime  2017-10-19T11:02:12+0800
     * @param     [array]                   $params [description]
     * @return    [type]                           [description]
     */
    public function handle($params)
    {
        $postData['sendtype'] = $params['timetype'];
        $postData['trade'] = $params['tradeType'];
        if ($params['itemtime']) {
            $postData['itemtime'] = $params['itemtime'];
        }
        $result = kernel::single('topmanageapi_statement_itemAnalyze')->ajaxTrade($postData);
        return $result;
    }
}



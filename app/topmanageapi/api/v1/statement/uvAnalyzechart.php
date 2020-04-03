<?php
/**
 * topapi
 *
 * -- statement.shop.uv.analyze.chart
 * -- 交易数据分析图表数据
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_uvAnalyzechart implements topmanageapi_interface_api{

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
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>'']
        ];
    }

/**
 * [交易数据分析图表数据]
 * @AuthorHTL
 * @DateTime  2017-10-19T10:12:58+0800
 * @param     [array]                   $params [接口参数]
 * @return    [array]                   $data   [图表数据]
 */
    public function handle($params)
    {
        $shopId = $params['oAuthInfo']['shop_id'];
        $postData['sendtype'] = $params['timetype'];
        $result = kernel::single('topmanageapi_statement_uvAnalyze')->ajaxTrade($postData,$shopId);
        return $result;
    }
}



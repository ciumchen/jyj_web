<?php
/**
 * topapi
 *
 * -- statement.shop.opera.info.chart
 * -- 商家运营概况
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_statement_operaInfochart implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '商家运营概况图表数据';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'timetype'    => ['type'=>'string', 'valid'=>'required|in:yesterday,beforday,week,month', 'example'=>'yesterday', 'desc'=>'数据查询日期:yesterday 昨日，beforday 前日，week 最近7天，month 最近30天', 'msg'=>''],
            'tradeType' =>['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'报表ajax请求的数据类型'],
            'compare'   =>['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'','description'=>'对比参数comparebefore：对比前一日，:compareweek:上周同期  只在昨日和前日数据中使用']
        ];
    }

/**
 * [商家运营概况图表数据]
 * @AuthorHTL
 * @DateTime  2017-10-19T10:12:58+0800
 * @param     [array]                   $params [description]
 * @return    [array]                           []
 */
    public function handle($params)
    {
        $postData['sendtype'] = $params['timetype'];
        $postData['trade'] = $params['tradeType'];
        if ($params['compare']) {
            $postData['compare'] = $params['compare'];
        }
        $result = kernel::single('topmanageapi_statement_operaInfo')->ajaxTrade($postData);
        return $result;
    }
}



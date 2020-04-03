<?php
/**
 * topapi
 *
 * -- index.goodsoldlist
 * -- 昨日销售商品排行
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_index_goodSoldList implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '昨日销售商品排行';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
        /*'timeType' => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'','description'=>'传入的时间类型 一共有6种(yesterday,beforday,week,month,selecttime,select)'],*/
        'limit' => ['type'=>'int','valid'=>'', 'default'=>'', 'example'=>'','description'=>'查询限制的条数']
        ];
    }

    /**
     * @return array 商品销售排行
     */
    public function handle($params)
    {
        //昨日销量排行第一
        $limit = $params['limit'];
        $timeType = $params['timeType'];
        $topParams = array('inforType'=>'item','timeType'=>'yesterday','limit'=>$limit);
        $topFiveItem =app::get('topshop')->rpcCall('sysstat.data.get',$topParams,'seller');
        if (!empty($topFiveItem['sysTrade']))
        {
            $result['sysTrade'] = $topFiveItem['sysTrade'];
        }else
        {
            $result['sysTrade'] = [];
        }
        return $result;
    }

}



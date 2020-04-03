<?php
/**
 * topmanageapi
 *
 * -- customer.aftersale.list
 * -- 退换货列表
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_afterSalelist implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '退换货列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'progress'  => ['type'=>'string', 'valid'=>'required' , 'example'=>'1','desc'=>'筛选条件默认为all:全部，0:等待审核，1:等待买家寄回，2:待确认发货，5:商家已收货，8:等待平台退款，3-4-6-7:已完成'],
            'created_time'  => ['type'=>'string', 'valid'=>'' , 'example'=>'','desc'=>'申请时间'],
            'aftersales_bn'  => ['type'=>'string', 'valid'=>'' , 'example'=>'','desc'=>'退换货编号'],
            'tid'  => ['type'=>'string', 'valid'=>'' , 'example'=>'','desc'=>'订单编号'],
            'aftersales_type'  => ['type'=>'string', 'valid'=>'' , 'example'=>'','desc'=>'退换货类型'],
            'title'  => ['type'=>'string', 'valid'=>'' , 'example'=>'','desc'=>'商品名称'],
            'pages_no'   => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'每页数据条数,默认10条'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段'],
            'pic_size'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'返回内容图像大小L:大图，M:中图 , S:小图 ,T:微图']
        ];
    }

    /**
     * @return array 商品列表
     */
    public function handle($params)
    {
        $this->__checkParams($params);
        if ($params['progress'])
        {
            $postParams['progress'] = $params['progress'];
        }
        $postParams['shop_id'] = $params['oAuthInfo']['shop_id'];
        $postParams['page_no'] = intval($params['pages_no'])? intval($params['pages_no']) : 1;
        $postParams['page_size'] = intval($params['page_size'])? intval($params['page_size']) : 10;
        $postParams['size'] = $params['pic_size'] ? $params['pic_size'] : '';
        if( !$params['fields'] || $params['fields'] == '*' )
        {
            $postParams['fields'] = 'aftersales_bn,aftersales_type,shop_id,created_time,oid,tid,num,progress,status,sku,gift_data';
        }
        else
        {
            $postParams['fields'] = $params['fields'];
        }
        $result = kernel::single('topmanageapi_customer_afterSales')->__searchListData($postParams);
        return $result;
    }

/**
 * [__checkParams 校验接口参数]
 * @AuthorHTL
 * @DateTime  2017-10-20T18:05:17+0800
 * @param     [type]                   &$params [接口参数]
 */
    private function __checkParams(&$params)
    {
        foreach($params as $key=>$value)
        {
            if( empty($value) && $key != "progress"  ) unset($params[$key]);

            if($key == "progress" )
            {
                if( $value == "all" )
                {
                    unset($params['progress']);
                }
                else
                {
                    $progress = explode('-',$params['progress']);
                    $params['progress'] = implode(',',$progress);
                }
            }

            if($key == "created_time")
            {
                $times = explode('-',$value);
                if(array_filter($times))
                {
                    $params['created_time']= json_encode($times);
                }
            }
        }
    }
}
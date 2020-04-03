
<?php
/**
 * topmanageapi
 *
 * -- customer.consult.list
 * -- 咨询列表
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_consultList implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '咨询列表';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'is_reply'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'有回复'],
            'type'  => ['type'=>'bool', 'valid'=>'' , 'example'=>'all','desc'=>'咨询类型item:商品 payment:支付方式 store_delivery:库存,配送 invoice: 发票,维修'],
            'pages_no'   => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'分页当前页数,默认为1'],
            'page_size' => ['type'=>'int',    'valid'=>'numeric',  'example'=>'', 'desc'=>'每页数据条数,默认10条'],
            'fields'    => ['type'=>'string', 'valid'=>'',  'example'=>'', 'desc'=>'需返回的字段']
        ];
    }

    /**
     * @return array 咨询列表
     */
    public function handle($params)
    {
        $postParams['shop_id']      = $params['oAuthInfo']['shop_id'];
        $postParams['page_no']      = $params['pages_no'] ? intval($params['pages_no']) : '1';
        $postParams['page_size']    = $params['page_size'] ? intval($params['page_size']) : '10';
        $postParams['fields']       = $params['fields'] ? $params['fields'] :'*';
        if ($params['type'])
        {
            $postParams['type'] = $params['type'];
        }

        if (isset($params['is_reply']) && $params['is_reply'] != 'all')
        {
            $postParams['is_reply'] = $params['is_reply'];
        }

        $result = kernel::single('topmanageapi_customer_consult')->getConsultList($postParams);
        return $result;

    }

}
<?php
/**
 * topmanageapi
 *
 * -- customer.comment.count
 * -- 评论概况
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_customer_commentCount implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '评论概况';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
        ];
    }

    /**
     * @return array 评论概况
     * @return string  countDsr.tally_dsr.num  实物与描述相符
     * @return string  countDsr.tally_dsr.percentage  实物与描述相符百分比
     * @return string  countDsr.attitude_dsr.num  服务质量
     * @return string  countDsr.attitude_dsr.percentage  服务质量百分比
     * @return string  countDsr.delivery_speed_dsr.num 发货速度
     * @return string  countDsr.delivery_speed_dsr.percentage 发货速度百分比
     * @return string  catDsrDiff.tally_dsr.percentage 描述相符
     * @return string  catDsrDiff.attitude_dsr.percentage 服务质量
     * @return string  catDsrDiff.delivery_speed_dsr.percentage 发货速度
     * @return array   $count  店铺近期评分状况
     */
    public function handle($params)
    {
        $shop_id = $params['oAuthInfo']['shop_id'];
        $postParams['shop_id'] = $shop_id;
        $postParams['catDsrDiff'] = true;
        $postParams['countNum'] = true;

        $dsrData = app::get('topshop')->rpcCall('rate.dsr.get', $postParams);
        if( !$dsrData )
        {
            $countDsr['tally_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['tally_dsr']['percentage'] = '100%';
            $countDsr['attitude_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['attitude_dsr']['percentage'] = '100%';
            $countDsr['delivery_speed_dsr']['num'] = sprintf('%.1f',5.0);
            $countDsr['delivery_speed_dsr']['percentage'] = '100%';
        }
        else
        {
            $countDsr['tally_dsr']['num'] = sprintf('%.1f',$dsrData['tally_dsr']);
            $countDsr['tally_dsr']['percentage'] = ($dsrData['tally_dsr']/5) * 100 .'%';
            $countDsr['attitude_dsr']['num'] = sprintf('%.1f',$dsrData['attitude_dsr']);
            $countDsr['attitude_dsr']['percentage'] = ($dsrData['attitude_dsr']/5) * 100 .'%';
            $countDsr['delivery_speed_dsr']['num'] = sprintf('%.1f',$dsrData['delivery_speed_dsr']);
            $countDsr['delivery_speed_dsr']['percentage'] = ($dsrData['delivery_speed_dsr']/5) * 100 .'%';
        }

        $result['countDsr'] = $countDsr;
        $result['countNum'] = $dsrData['countNum'];
        $result['catDsrDiff'] = $dsrData['catDsrDiff'];
        $result['count'] = app::get('topshop')->rpcCall('rate.count',array('shop_id' => $shop_id));
        $shop_type = app::get('topshop')->rpcCall('shop.get',array('shop_id'=>$shop_id));
        $result['shop_type'] = $shop_type['shop_type'];
        return $result;

    }

}
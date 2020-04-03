<?php
class topmanageapi_statement_operaInfo{

    /**
     * [get_itemAnalyze_data 运营概况分析]
     * @AuthorHTL
     * @DateTime  2017-10-18T17:49:39+0800
     * @param     [array]                   $postSend  [接口参数]
     * @param     [string]                  $type      [时间类型]
     * @return    [array]                               [商品统计数据]
     */
    public function get_operaInfo_data($type)
    {
        if(!$type || !in_array($type,array('yesterday','beforday','week','month')))
        {
            $type='yesterday';
        }
        $postSend['sendtype'] = $type;
        //api参数
        $all = $this->__getParams('all',$postSend,'trade');
        //获取交易数据
        $data = app::get('topshop')->rpcCall('sysstat.data.get',$all);
        //业务数据
        $sysDataInfo = $this->getDataInfo();
        //商品排行
        $sysItemInfo = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'item','timeType'=>$type,'limit' =>5,'start'=>0));
        $result['sysItemInfo'] = $sysItemInfo['sysTrade']?$sysItemInfo['sysTrade']:$sysItemInfo['sysTradeData'];
        //格式化商品排行图片url
        foreach ($result['sysItemInfo'] as $key => $value) {

            foreach ($value as $k => $v) {
                if ($k ==  'pic_path') {
                    //默认小图
                    $value[$k] = base_storager::modifier($v,'');
                }
            }
            $result['sysItemInfo'][$key] = $value;
        }
        //店铺流量数据
        $result['traffic_disabled'] = config::get('stat.disabled');
        if(!$result['traffic_disabled']){
            $all['shop_id'] = $shopId;
            $result['trafficData'] = app::get('topshop')->rpcCall('sysstat.traffic.data.get',$all);
        }
        $result['sysstat'] = $data['sysstat'];
        $result['sendtype'] = $type;
        $result['sysDataInfo'] = $sysDataInfo;
        return $result;
    }

//获取页面下面数据
    private function getDataInfo()
    {
        //昨日数据
        $yesterday = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'yesterday'));
        //前日数据
        $before = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'beforday'));
        //本周数据
        $week = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'week'));
        //本月数据
        $month = app::get('topshop')->rpcCall('sysstat.data.get',array('inforType'=>'trade','timeType'=>'month'));
        $data = array(
            'yesterday'=>$yesterday['sysstat'],
            'beforInfo'=>$before['sysstat'],
            'week'=>$week['sysstat'],
            'month'=>$month['sysstat'],
        );

        return $data;
    }

//api参数组织
    private function __getParams($type,$postSend,$objType,$data=null)
    {
        if($type=='all')
        {
            $params = array(
                'inforType'=>$objType,
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['starttime'],
                'endtime'=>$postSend['endtime'],
                'dataType'=>$type
            );
        }
        elseif($type=='graphall')
        {
            $params = array(
                'inforType'=>$objType,
                'tradeType'=>$postSend['trade'],
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['starttime'],
                'endtime'=>$postSend['endtime'],
                'dataType'=>$type
            );
        }
        return $params;
    }

    /**
     *图表用
     * @param null
     * @return array
     */

    public function ajaxTrade($postData)
    {
        //api的参数
        $all = $this->__getParams('graphall',$postData,'trade');
        $datas =  app::get('topshop')->rpcCall('sysstat.data.get',$all);

        return $datas;
    }
}
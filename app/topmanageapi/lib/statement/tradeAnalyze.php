<?php
class topmanageapi_statement_tradeAnalyze{

    /**
     * [get_itemAnalyze_data 订单销售分析]
     * @AuthorHTL
     * @DateTime  2017-10-18T17:49:39+0800
     * @param     [array]                   $postSend  [接口参数]
     * @param     [string]                  $type      [时间类型]
     * @return    [array]                               [商品统计数据]
     */
    public function get_tradeAnalyze_data($postSend,$type)
    {
        $objFilter = kernel::single('sysstat_data_filter');
        $postParams = $objFilter->filter($postSend);
        if($postSend['compare'])
        {
            $result['compare'] = $postSend['compare'];
        }
        if(!$type || !in_array($type,array('yesterday','beforday','week','month','selecttime','select')))
        {
            $type='yesterday';
        }
        
        $postSend['sendtype'] = $type;
        //api参数
        $all = $this->__getParams('all',$postSend,'trade');
        $notAll = $this->__getParams('notall',$postSend,'trade',$postParams);
        //获取交易数据
        $data = app::get('topshop')->rpcCall('sysstat.data.get',$notAll);
        if($type=='selecttime')
        {
            $result['sysstat'] = $data['sysstat'];
        }
        else
        {
            $result['sysstat'] = $data['sysstat']['commonday'];
        }

        //获取页面显示的时间
        $pagetime = app::get('topshop')->rpcCall('sysstat.datatime.get',$all);
        //api的参数
        $countAll = $this->__getParams('notall',$postSend,'tradecount',$postParams);

        //处理翻页数据
        $countData = app::get('topshop')->rpcCall('sysstat.data.get',$countAll);
        $count = $countData['count'];
        $result['pages_no']     = $postSend['pages'];
        $result['page_size']    = $postParams['limit'];
        $result['count']        = $count;
        $result['sendtype']     = $type;
        $result['pagetime']     = $pagetime;
        if (!empty($data['sysTradeData'])) {
            $num = $postParams['limit'];
            $b_num = ($postSend['pages']-1)*10;
            if (($b_num+$num) > $count) {
               $num = $count - $b_num;
            }
            for ($i=0; $i <$num; $i++) { 
                $data['sysTradeData'][$i]['num'] = $i+$b_num+1;
            }
        }
        
        $result['sysTradeData'] = $data['sysTradeData'];
        return $result;
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
        elseif($type=='notall')
        {
            $params = array(
                'inforType'=>$objType,
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['starttime'],
                'endtime'=>$postSend['endtime'],
                'dataType'=>$type,
                'limit'=>intval($data['limit']),
                'start'=>intval($data['start'])
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
     * 异步获取数据  图表用
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
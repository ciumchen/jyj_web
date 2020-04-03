<?php
class topmanageapi_statement_itemAnalyze{

    /**
     * [get_itemAnalyze_data 商品销售分析]
     * @AuthorHTL
     * @DateTime  2017-10-18T17:49:39+0800
     * @param     [array]                   $postSend  [接口参数]
     * @param     [string]                  $type      [时间类型]
     * @return    [array]                               [商品统计数据]
     */
    public function get_itemAnalyze_data($postSend,$type)
    {
        $objFilter = kernel::single('sysstat_data_filter');
        $postParams = $objFilter->filter($postSend);
        $itemtime = array('starttime'=>$postSend['itemtime']);
        if(!$type || !in_array($type,array('yesterday','beforday','week','month','selecttime')))
        {
            $type='yesterday';
        }
        
        $postSend['sendtype'] = $type;
        //api参数
        $all = $this->__getParams('all',$postSend,'item');
        $notAll = $this->__getParams('notall',$postSend,'item',$postParams);
        $itemInfo = app::get('topshop')->rpcCall('sysstat.data.get',$notAll);
        //获取交易数据
        $data = app::get('topshop')->rpcCall('sysstat.data.get',$notAll);
        $topParams = array('inforType'=>'item','timeType'=>$type,'starttime'=>$postSend['itemtime'],'limit'=>5);
        $topFiveItem = app::get('topshop')->rpcCall('sysstat.data.get',$topParams);
        foreach ($topFiveItem['sysTrade'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k ==  'pic_path') {
                    //默认小图
                    $value[$k] = base_storager::modifier($v,'');
                }
            }
            $topFiveItem['sysTrade'][$key] = $value;
        }
        //获取页面显示的时间
        $pagetime = app::get('topshop')->rpcCall('sysstat.datatime.get',$all);
        //api的参数
        $countAll = $this->__getParams('notall',$postSend,'tradecount',$postParams);

        //处理翻页数据
        $countData = app::get('topshop')->rpcCall('sysstat.data.get',$countAll);
        $count = $countData['count'];
        if($type=='selecttime')
        {
            $pagetime = $pagetimes['before'];
        }
        else
        {
            $pagetime = $pagetimes;
        }

        $result['pages_no']     = $postSend['pages'];
        $result['page_size']    = $postParams['limit'];
        $result['count']        = $count;
        $result['sendtype']     = $type;
        $result['pagetime']     = $pagetime;
        $result['itemInfo']     = $itemInfo['sysTrade']?$itemInfo['sysTrade']:$itemInfo['sysTradeData'];
        //为数据添加序号
        if (!empty($result['itemInfo']))
        {
            $num = $postParams['limit'];
            $b_num = ($postSend['pages']-1)*10;
            if (($b_num+$num) > $count)
            {
               $num = $count - $b_num;
            }
            for ($i=0; $i <$num; $i++)
            { 
                $result['itemInfo'][$i]['num'] = $i+$b_num+1;
            }
        }
        $result['traffic_disabled'] = config::get('stat.disabled');
        if(!$result['traffic_disabled'])
        {
            $itemids = implode(',',array_column($result['itemInfo'], 'item_id'));
            if($itemids){
                $apiData = $notAll;
                $apiData['itemids'] = $itemids;
                $result['uvData'] = app::get('topshop')->rpcCall('sysstat.traffic.data.get',$apiData);
            }
        }
        //优化商品详细中图片url
        foreach ($result['itemInfo'] as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k ==  'pic_path') {
                    //默认小图
                    $value[$k] = base_storager::modifier($v,'');
                }
                if ($k == 'item_id')
                {
                    $value['uvData'] = $result['uvData'][$v];
                }
            }
            $result['itemInfo'][$key] = $value;
        }
        $result['topFiveItem'] = $topFiveItem['sysTrade'];
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
                'starttime'=>$postSend['itemtime'],
            );
        }
        elseif($type=='notall')
        {
            $params = array(
                'inforType'=>$objType,
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['itemtime'],
                'limit'=>intval($data['limit']),
                'start'=>intval($data['start'])
            );
        }
        elseif($type=='itemgraphall')
        {
            $params = array(
                'inforType'=>$objType,
                'tradeType'=>$postSend['trade'],
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['itemtime'],
                'dataType'=>$type,
                'limit'=>intval($postSend['limit']),
                'orderBy'=>$postSend['orderBy'],
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
        $orderBy = $postData['trade'].' '.'DESC';
        $postData['orderBy'] = $orderBy;
        $postData['limit'] = 10;

        $grapParams = $this->__getParams('itemgraphall',$postData,'item');
        $datas =  app::get('topshop')->rpcCall('sysstat.data.get',$grapParams);

        $ajaxdata = array('dataInfo'=>$data,'datas'=>$datas);

        return $ajaxdata;
    }
}
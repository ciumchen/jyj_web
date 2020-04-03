<?php

class topmanageapi_statement_orderAftersaleAnalyze{

    public function get_orderAftersale_data($postSend,$type)
    {
        $objFilter = kernel::single('sysstat_data_filter');
        $params = $objFilter->filter($postSend);
        if(!$type || !in_array($type,array('yesterday','beforday','week','month','selecttime','select')))
        {
            $type='yesterday';
        }
        $postSend['sendtype'] = $type;

        //api参数
        $all = $this->__getParams('all',$postSend,'trade');
        $notAll = $this->__getParams('notall',$postSend,'trade',$params);
        //获取数据
        $data = app::get('topshop')->rpcCall('sysstat.data.get',$notAll);
        if($type=='selecttime')
        {
            $result['sysstat'] = $data['sysstat'];
        }
        else
        {
            $result['sysstat'] = $data['sysstat']['commonday'];
        }

        //退货前十的商品
        $refundParams = array('inforType'=>'item','timeType'=>$type,'starttime'=>$postSend['starttime'],'limit'=>10,'orderBy'=>'refundnum DESC');
        $refundToptenItem = app::get('topshop')->rpcCall('sysstat.data.get',$refundParams);
    
        $result['refundToptenItem'] = $refundToptenItem['sysTrade'];
        //换货前十的商品
        $changingParams = array('inforType'=>'item','timeType'=>$type,'starttime'=>$postSend['starttime'],'limit'=>10,'orderBy'=>'changingnum DESC');
        $changingToptenItem = app::get('topshop')->rpcCall('sysstat.data.get',$changingParams);
        $result['changingToptenItem'] = $changingToptenItem['sysTrade'];

        //获取页面显示的时间
        $pagetime = app::get('topshop')->rpcCall('sysstat.datatime.get',$all);
        $result['sendtype'] = $type;
        $result['pagetime'] = $pagetime;
        $result['sysTradeData'] = $data['sysTradeData'];
        return $result;
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
}
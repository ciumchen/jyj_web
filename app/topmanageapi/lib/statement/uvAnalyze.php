<?php
class topmanageapi_statement_uvAnalyze{



    public function get_uvAnalyze_data($postSend,$type,$shopId)
    {
        $objFilter = kernel::single('sysstat_data_filter');
        $postParams = $objFilter->filter($postSend);
        if(!$type || !in_array($type,array('yesterday','beforday','week','month')))
        {
            $type='yesterday';
        }
        
        $postSend['sendtype'] = $type;
        //api参数
        $notAll = $this->__getParams('notall',$postSend,'yesterdayRank',$postParams,$shopId);
        //获取交易数据
        $data = app::get('topshop')->rpcCall('sysstat.traffic.data.get',$notAll);
        foreach ($data['trafficData'] as $key => $value) {
            if (is_array($value))
            {
                foreach ($value as $k => $v)
                {
                    if ($k == 'page_rel_id')
                    {
                        $params['item_id'] = $v;
                        $params['shopId']  = $shopId;
                        $params['fields']  = "title,image_default_id";
                        $item= app::get('topshop')->rpcCall('item.get',$params);
                        $value['item_title'] = $item['title'];
                        $value['pic_path'] = base_storager::modifier($item['image_default_id'],'');;
                    }
                }
            }
            $data['trafficData'][$key] = $value;
        }
        $result['trafficData'] = $data['trafficData'];
        $result['shop_id'] = $shopId;
        $result['sendtype'] = $type;

        //分页
        $count = $data['count'];
        if($count>0) $total = ceil($count/$postParams['limit']);
        $current = $postSend['pages'] ? $postSend['pages'] : 1;

        $result['pages_no']     = $postSend['pages'];
        $result['page_size']    = $postParams['limit'];
        $result['count']        = $count;
        $result['sendtype']     = $type;
        return $result;
    }


    //api参数组织
    private function __getParams($type,$postSend,$objType,$data=null,$shopId)
    {
        if($type=='all')
        {
            $params = array(
                'inforType'=>$objType,
                'timeType'=>$postSend['sendtype'],
                'starttime'=>$postSend['starttime'],
                'endtime'=>$postSend['endtime'],
                'dataType'=>$type,
                'shop_id' =>$shopId,
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
                'start'=>intval($data['start']),
                'shop_id' =>$shopId,
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
                'dataType'=>$type,
                'shop_id' =>$shopId,
            );
        }
        return $params;
    }


        /**
     * 异步获取数据  图表用
     * @param null
     * @return array
     */

    public function ajaxTrade($postData,$shopId)
    {
        //api的参数
        $all = $this->__getParams('graphall',$postData,'weball',null,$shopId);
        $datas =  app::get('topshop')->rpcCall('sysstat.traffic.data.get',$all);
        return $datas;
    }
}
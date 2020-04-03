<?php
class topmanageapi_customer_appeal{

/**
 * [getAppealList 获取申诉列表数据]
 * @param  [type] $params [接口参数]
 */
    public function getAppealList($params)
    {
        //获取申诉列表数据
        $data = app::get('topshop')->rpcCall('rate.list.get', $params);
        //讲图片地址由字符串转成数组
        foreach( $data['trade_rates'] as $k=>$row)
        {
            $userId[] = $row['user_id'];

            if( $row['appeal']['evidence_pic'] )
            {
                $pic = explode(',', $row['appeal']['evidence_pic']);
                foreach ($pic as $key => $value)
                {
                    $pic[$key] = base_storager::modifier($value,'');
                }
                $data['trade_rates'][$k]['appeal']['evidence_pic'] = $pic;
            }
        }
        $result['rate'] = $data['trade_rates'];
        //如果有用户id 获取用户名称
        if( $userId )
        {
            $result['userName'] = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => implode(',', $userId)]);
        }

        $result['total'] = $data['total_results'];
        return $result;
    }

/**
 * [getAppealInfo 获取申诉详情]
 * @param  [type] $params [接口参数]
 * @return [type]         [申诉详情信息]
 */
    public function getAppealInfo($params)
    {
        $data = app::get('topshop')->rpcCall('rate.get', $params);
        if( $data['appeal']['evidence_pic'] )
        {
            $pic = explode(',',$data['appeal']['evidence_pic']);
            foreach ($pic as $key => $value)
            {
                $pic[$key] = base_storager::modifier($value,'');
            }
            $data['appeal']['evidence_pic'] = $pic;
        }
        if ($data['item_pic'])
        {
            $data['item_pic'] = base_storager::modifier($data['item_pic']);
        }
        if($data['rate_pic'])
        {
            $rate_pic = explode(',',$data['rate_pic']);
            foreach ($pic as $key => $value)
            {
                $rate_pic[$key] = base_storager::modifier($value,'');
            }
            $data['rate_pic'] = $rate_pic;
        }
        $result['rate'] = $data;
        return $result;
    }

/**
 * [appeal 评价申诉]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function appeal($params,$shop_id)
    {
        try {
            $flag =kernel::single('sysrate_appeal')->add($params,$shop_id);
            $status = $flag ? 'success' : 'error';
            $msg = $flag ? '申诉已提交，请耐心等待，我们将会在10个工作日内给予审核回复，谢谢' : '申诉提交失败，请重新再试';
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $status = 'error';
        }
        return ['status'=>$status,'message'=>$msg];
    }

}
<?php
class topmanageapi_customer_comment{


/**
 * [getCommentList 获取评价列表]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function getCommentList($params)
    {
        //声明调用角色
        $params['role'] = 'seller';
        $data = app::get('topshop')->rpcCall('rate.list.get',$params);
        foreach( $data['trade_rates'] as $k=>$row)
        {
            $userId[] = $row['user_id'];
            if( $row['rate_pic'] )
            {
                $pic = explode(',', $row['rate_pic']);
                foreach ($pic as $key => $value)
                {
                    $pic[$key] = base_storager::modifier($value,'');
                }
                $data['trade_rates'][$k]['rate_pic'] = $pic;
            }
            if($row['item_pic'])
            {
                $data['trade_rates'][$k]['item_pic'] = base_storager::modifier($row['item_pic'],'');
            }
            if( $row['append']['append_rate_pic'] )
            {
                $data['trade_rates'][$k]['append']['append_rate_pic'] = explode(',',$row['append']['append_rate_pic']);
            }
        }
        $result['rate'] = $data['trade_rates'];

        if( $userId )
        {
            $result['userName'] = app::get('topshop')->rpcCall('user.get.account.name', ['user_id' => implode(',', $userId)]);
        }

        $result['total'] = $data['total_results'];
        $result['page_no'] = $params['page_no'];
        $result['page_size'] = $params['page_size'];
        return $result;
    }

/**
 * [getCommentInfo 获取评价详情]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function getCommentInfo($params)
    {
        $data =app::get('topshop')->rpcCall('rate.get', $params);
        if (empty($data))
        {
            return ['status'=>'error','massage'=>'详细信息有误'];
        }
        if($data['rate_pic'])
        {
            $pic = explode(',',$data['rate_pic']);
            foreach ($pic as $key => $value) {
                    $pic[$key] = base_storager::modifier($value,'');
                }
            $data['rate_pic'] = $pic;
        }

        if( $data['append']['append_rate_pic'] )
        {
            $pic = explode(',',$data['append']['append_rate_pic']);
            foreach ($pic as $key => $value) {
                    $pic[$key] = base_storager::modifier($value,'');
                }
            $data['append']['append_rate_pic'] = $pic;
        }

        return $data;

    }

    public function reply($params)
    {
        try {
            if ($params['type'] == 'add') {
                $flag = kernel::single('sysrate_traderate')->reply($params['rate_id'], $params['reply_content'],$params['shop_id']);
                $status = $flag ? 'success' : 'error';
                $msg    = $flag ? '回复成功' : '回复失败';
            }else
            {
                $result = app::get('topshop')->rpcCall('rate.append.reply', $params);
            }
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $status = 'error';
        }
        return ['status'=>$status,'message'=>$msg];
    }










}
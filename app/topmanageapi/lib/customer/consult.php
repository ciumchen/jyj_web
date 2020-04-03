<?php

class topmanageapi_customer_consult
{

/**
 * [getConsultList 获取]
 * @param  [type] $params [接口参数]
 * @return [type]         [咨询列表数据]
 */
    public function getConsultList($params)
    {
        $data = app::get('topshop')->rpcCall('rate.gask.list', $params);
        $result['gask'] = $data['lists'];
        $result['total'] = $data['total_results'];
        $result['page_no'] = $params['page_no'];
        $result['page_size'] = $params['page_size'];
        return $result;
    }

/**
 * [doReply 回复咨询内容]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function doReply($params)
    {
        try {
            $flag = app::get('topshop')->rpcCall('rate.gask.reply',$params);
            $status = $flag ? 'success' : 'error';
            $msg = $flag ? '回复成功' : '回复失败';
        } catch (Exception $e) {
            $status = 'error';
            $msg = $e->getMessage();
        }
        return ['status'=>$status,'message'=>$message];
    }


/**
 * [doDisplay 咨询是否显示至商品详情页]
 * @param  [type] $params [description]
 * @return [type]         [description]
 */
    public function doDisplay($params)
    {
        try {
            $flag = app::get('topshop')->rpcCall('rate.gask.display',$params);
            $status = $flag ? 'success' : 'error';
            $msg = $flag ? '操作成功' : '操作失败';
        } catch (Exception $e) {
            $status = 'error';
            $msg = $e->getMessage();
        }
        return ['status'=>$status,'message'=>$msg];
    }


    public function daDelete($params)
    {
        try {
            $flag = app::get('topshop')->rpcCall('rate.gask.delete',$params);
            $status = $flag ? 'success' : 'error';
            $msg = $flag ? '删除回复成功': '删除回复失败';
        } catch (Exception $e) {
            $status = 'error';
            $msg = $e->getMessage();
        }

        return ['status'=>$status,'message'=>$message];
    }















}
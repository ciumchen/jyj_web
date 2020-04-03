<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class  sysaftersales_ctl_admin_return extends desktop_controller
{

    /**
     * @brief  通过iframe把时间参数传进去
     *
     * @return
     */
    public function index()
    {
        // 准备数据
        $getData = input::get ();
        $searchParams = array ();

        // 表单搜索
        $actUrl = '?app=sysaftersales&ctl=admin_return&act=index';
        $this->pagedata ['form_url'] = $actUrl;
        // 商家列表
        $objLibSett = kernel::single ('sysclearing_data_get');
        $shopList = $objLibSett->getShopList ();
        $shopList [- 1] = '全部';
        ksort ($shopList);

        // 设置页面变量
        $this->pagedata ['options'] = $shopList;
        $this->pagedata ['time_start'] = strtotime (date ('Y-m-d 00:00:00', strtotime ('-1 month')));
        $this->pagedata ['time_end'] = strtotime (date ('Y-m-d 23:59:59'));

        if (isset ($getData ['issearch']) && $getData ['issearch'] == 1)
        {
            // 搜索变量
            $searchParams['time_start'] = $this->pagedata ['time_start'] = isset ($getData ['time_start']) ? $getData ['time_start'] : $this->pagedata ['time_start'];
            $searchParams['time_end'] = $this->pagedata ['time_end'] = isset ($getData ['time_end']) ? $getData ['time_end'] : $this->pagedata ['time_end'];
            $searchParams['shop_id'] = $this->pagedata ['shop_id'] = isset ($getData ['shop_id']) ? $getData ['shop_id'] : - 1;
            $searchParams['progress'] = $this->pagedata ['progress'] = isset ($getData ['progress']) ? $getData ['progress'] : - 1;
            $searchParams['status'] = $this->pagedata ['status'] = isset ($getData ['status']) ? $getData ['status'] : - 1;
            $searchParams['aftersales_type'] = $this->pagedata ['aftersales_type'] = isset ($getData ['aftersales_type']) ? $getData ['aftersales_type'] : - 1;
        }

        // 导出条件
        // $this->pagedata['export_filter'] = json_encode($searchParams);
        // if($this->has_permission('export')){
            // $top_extra_view = array('sysclearing'=>'sysclearing/admin/index_header.html');
        // }

        // return $this->finder('sysaftersales_mdl_aftersales',array(
                // 'title'=>app::get('sysclearing')->_('退货报表'),
                // 'use_buildin_filter' => true,
                // 'use_buildin_delete' => false,
                // 'use_buildin_refresh' => false,
                // 'use_buildin_setcol' => false,
                // 'use_buildin_selectrow' =>false,
                // 'base_filter' =>$searchParams,
                // 'top_extra_view'=>$top_extra_view,
        // ));		
       // kernel::single('sysstat_tasks_operatorday')->exec();exit();
        $params = array(
            'time_start'=>date('Y-m-d 00:00:00', strtotime('-1 day')),
            'time_end'=>date('Y-m-d 23:59:59', strtotime('1 day'))
        );
        $pagedata['time_start'] = $params['time_start']; 
        $pagedata['time_end'] = $params['time_end'];
        return $this->page('sysaftersales/admin/report/returnlist.html',$pagedata);
    }

    //报表
    public function collectItemAnalysis()
    {
        $data = input::get();

         // 商家列表
        $objLibSett = kernel::single ('sysclearing_data_get');
        $shopList = $objLibSett->getShopList ();
        $shopList [- 1] = '全部';
        ksort ($shopList);

        // 设置页面变量
        $pagedata['options'] = $shopList;
				if(strtotime($data['time_start'])>strtotime($data['time_end']))
				{
					throw new \LogicException(app::get('sysstat')->_("开始时间必须小于结束时间"));
				}
				if($data['timeType'])
				{
					$timeRange = kernel::single('sysstat_desktop_commonData')->getTimeRangeByType($data['timeType']);
					$timeStart = strtotime($timeRange['time_start']);
					$timeEnd = strtotime($timeRange['time_end']);
				}
				else
				{
					$timeStart = strtotime($data['time_start']);
					$timeEnd = strtotime($data['time_end']);
				}
	$filter = array();
				$filter = array(
					'created_time|between'=>array($timeStart,$timeEnd),

				);
				$filterSql .= ' AND (A.created_time BETWEEN '.$timeStart.' AND '.$timeEnd.')';	
				if(isset($data['progress']) && $data['progress']!='all'){
						$filter['progress'] = $data['progress'];
						$filterSql .= ' AND A.progress='.$data['progress'];	
				}		
				if(isset($data['status']) && $data['status']!='all'){
						$filter['status'] = $data['status'];
						$filterSql .= ' AND A.status='.$data['status'];	
				}		
				if(isset($data['shop_id']) && $data['shop_id']!='-1'){
						$filter['shop_id'] = $data['shop_id'];
												$filterSql .= ' AND A.shop_id='.$data['shop_id'];	
				}		
				if(isset($data['aftersales_type']) && $data['aftersales_type']!='all'){
						$filter['aftersales_type'] = $data['aftersales_type'];
							$filterSql .= ' AND A.aftersales_type="'.$data['aftersales_type'].'"';	
				}
				$limit = $data['storeLimit']?$data['storeLimit']:5;
				//获取商品收藏排行数据
				$sql = "SELECT A.*,T.created_time octime,R.refund_fee,O.price buyprice,O.num buynum FROM sysaftersales_aftersales A 
				LEFT JOIN systrade_trade T ON T.tid=A.tid 
				LEFT JOIN sysaftersales_refunds R ON R.aftersales_bn=A.aftersales_bn 
				LEFT JOIN systrade_order O ON O.oid=A.oid 
				WHERE 1=1 AND O.item_id=A.item_id AND O.sku_id=A.sku_id ".$filterSql; 
				$sql .= ' ORDER BY octime DESC,tid DESC ,item_id ASC LIMIT '.$limit;
				// $collectItem = app::get('sysaftersales')->model('aftersales')->getList('*',$filter,0,$limit,'created_time DESC');    
				$collectItem = app::get('sysaftersales')->database()->executeQuery($sql)->fetchAll(); 
				//echo '<pre>';var_dump($collectItem);
				foreach($collectItem as $k=>$v){
					// $orderdata = app::get('systrade')->model('order')->dump(array('oid'=>$v['oid'],'item_id'=>$v['item_id'],'sku_id'=>$v['sku_id']),'price,num');
					// $collectItem[$k]['buyprice'] = $orderdata['price'];
					// $collectItem[$k]['buynum'] = $orderdata['num'];
										$collectItem[$k]['octime'] = date('Y-m-d H:i:s',$v['octime']);
					$collectItem[$k]['created_time'] = date('Y-m-d H:i:s',$v['created_time']);
				}
				$pagedata['collectItemData'] = $collectItem;
				$pagedata['time_start'] = date('Y/m/d',$timeStart);
				$pagedata['time_end'] = date('Y/m/d',$timeEnd);		

        return view::make('sysaftersales/admin/report/collectitemAnalysis.html',$pagedata);
    }
    //数据显示
    public function dataShow()
    {
        return $this->finder('sysaftersales_aftersales',array(
            'use_buildin_delete' => false,
            'use_buildin_filter'=>true,
            'use_buildin_export' => true,
            'title' => app::get('sysshop')->_('退换货报表'),
        ));
    }

     //异步请求获取的数据
    public function ajaxData()
    {
        $data = input::get();
        
        try
        {
				if(strtotime($data['time_start'])>strtotime($data['time_end']))
				{
					throw new \LogicException(app::get('sysstat')->_("申请开始时间必须小于结束时间"));
				}
				if($data['timeType'])
				{
					$timeRange = kernel::single('sysstat_desktop_commonData')->getTimeRangeByType($data['timeType']);
					$timeStart = strtotime($timeRange['time_start']);
					$timeEnd = strtotime($timeRange['time_end']);
				}
				else
				{
					$timeStart = strtotime($data['time_start']);
					$timeEnd = strtotime($data['time_end']);
				}	



	$filter = array();
				$filter = array(
					'created_time|between'=>array($timeStart,$timeEnd),

				);
				if(strtotime($data['time_start_order'])>strtotime($data['time_end_order']))
				{
					throw new \LogicException(app::get('sysstat')->_("下单开始时间必须小于结束时间"));
				}
				$filterSql .= ' AND (A.created_time BETWEEN '.$timeStart.' AND '.$timeEnd.')';	
				if($data['time_start_order'] || $data['time_end_order']){
					$timeStart_order = strtotime($data['time_start_order']);
					$timeEnd_order = strtotime($data['time_end_order']);
					$filterSql .= ' AND (T.created_time BETWEEN '.$timeStart_order.' AND '.$timeEnd_order.')';	
				}
				if(isset($data['progress']) && $data['progress']!='all'){
						$filter['progress'] = $data['progress'];
						$filterSql .= ' AND A.progress='.$data['progress'];	
				}		
				if(isset($data['status']) && $data['status']!='all'){
						$filter['status'] = $data['status'];
						$filterSql .= ' AND A.status='.$data['status'];	
				}		
				if(isset($data['shop_id']) && $data['shop_id']!='-1'){
						$filter['shop_id'] = $data['shop_id'];
												$filterSql .= ' AND A.shop_id='.$data['shop_id'];	
				}		
				if(isset($data['aftersales_type']) && $data['aftersales_type']!='all'){
						$filter['aftersales_type'] = $data['aftersales_type'];
							$filterSql .= ' AND A.aftersales_type="'.$data['aftersales_type'].'"';	
				}
				$limit = $data['storeLimit']?$data['storeLimit']:5;
				//获取商品收藏排行数据
				$sql = "SELECT A.*,T.created_time octime,R.refund_fee,O.price buyprice,O.num buynum FROM sysaftersales_aftersales A 
				LEFT JOIN systrade_trade T ON T.tid=A.tid 
				LEFT JOIN sysaftersales_refunds R ON R.aftersales_bn=A.aftersales_bn 
				LEFT JOIN systrade_order O ON O.oid=A.oid 
				WHERE 1=1 AND O.item_id=A.item_id AND O.sku_id=A.sku_id ".$filterSql; 
				$sql .= ' ORDER BY octime DESC,tid DESC ,item_id ASC LIMIT '.$limit;
				// $collectItem = app::get('sysaftersales')->model('aftersales')->getList('*',$filter,0,$limit,'created_time DESC');    
				$collectItem = app::get('sysaftersales')->database()->executeQuery($sql)->fetchAll(); 
				//echo '<pre>';var_dump($collectItem);
				foreach($collectItem as $k=>$v){
					// $orderdata = app::get('systrade')->model('order')->dump(array('oid'=>$v['oid'],'item_id'=>$v['item_id'],'sku_id'=>$v['sku_id']),'price,num');
					// $collectItem[$k]['buyprice'] = $orderdata['price'];
					// $collectItem[$k]['buynum'] = $orderdata['num'];
					$collectItem[$k]['octime'] = date('Y-m-d H:i:s',$v['octime']);
					$collectItem[$k]['created_time'] = date('Y-m-d H:i:s',$v['created_time']);
				}
				$pagedata['collectItemData'] = $collectItem;
				$pagedata['time_start'] = date('Y/m/d',$timeStart);
				$pagedata['time_end'] = date('Y/m/d',$timeEnd);		
				$pagedata['time_start_order'] = date('Y/m/d',$timeStart_order);
				$pagedata['time_end_order'] = date('Y/m/d',$timeEnd_order);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }
        //echo '<pre>';print_r($pagedata);exit();
        $pagedata['dataType'] = $data['dataType'];
        return  response::json($pagedata) ;
    }


     //异步请求时间获取的数据
    public function ajaxTimeData()
    {
        $data = input::get();
        //echo '<pre>';print_r($data);exit();
        try
        {
				if(strtotime($data['time_start'])>strtotime($data['time_end']))
				{
					throw new \LogicException(app::get('sysstat')->_("开始时间必须小于结束时间"));
				}
				if($data['timeType'])
				{
					$timeRange = kernel::single('sysstat_desktop_commonData')->getTimeRangeByType($data['timeType']);
					$timeStart = strtotime($timeRange['time_start']);
					$timeEnd = strtotime($timeRange['time_end']);
				}
				else
				{
					$timeStart = strtotime($data['time_start']);
					$timeEnd = strtotime($data['time_end']);
				}
				$filter = array(
					'created_time'=>$timeStart,
				);
				if(isset($data['progress']) && $data['progress']!='all'){
						$filter['progress'] = $data['progress'];
				}		
				if(isset($data['status']) && $data['status']!='all'){
						$filter['status'] = $data['status'];
				}		
				if(isset($data['shop_id']) && $data['shop_id']!='-1'){
						$filter['shop_id'] = $data['shop_id'];
				}		
				if(isset($data['aftersales_type']) && $data['aftersales_type']!='all'){
						$filter['aftersales_type'] = $data['aftersales_type'];
				}
				$limit = $data['storeLimit']?$data['storeLimit']:5;
				//获取商品收藏排行数据

				$collectItem = app::get('sysaftersales')->model('aftersales')->getList('*',$filter,0,$limit,'created_time DESC');
				$pagedata['collectItemData'] = $collectItem;
				$pagedata['time_start'] = date('Y/m/d',$timeStart);
				$pagedata['time_end'] = date('Y/m/d',$timeEnd);
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg);
        }

        $pagedata['dataType'] = $data['dataType'];
        return  response::json($pagedata) ;
    }



}

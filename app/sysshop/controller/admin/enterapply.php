<?php
class sysshop_ctl_admin_enterapply extends desktop_controller{

    public $workground = 'sysshop.workground.shoptype';
    public $shoptype = array(
        'flag'=>'品牌旗舰店',
        'brand'=>'品牌专卖店',
        'cat'=>'类目专营店',
        'store'=>'多品类通用店',
    );

    public function index()
    {
        $actions = array(
            array(
                'label'=>app::get('sysshop')->_('入驻协议配置'),
                'href' => '?app=sysshop&ctl=admin_enterapply&act=setProtocol',
                'target'=>'dialog::{title:\''.app::get('sysshop')->_('入驻协议配置').'\',width:500,height:500}'
            ),
        );
        return $this->finder('sysshop_mdl_enterapply',array(
            'title' => '入驻申请列表',
            'actions' => $actions,

        ));
    }

    public function license()
    {

        if( $_POST['license'] )
        {
            $this->begin();
            app::get('sysshop')->setConf('sysshop.register.setting_sysshop_license',$_POST['license']);
            $this->end(true, app::get('sysshop')->_('当前配置修改成功！'));
        }
        $pagedata['license'] = app::get('sysshop')->getConf('sysshop.register.setting_sysshop_license');

        return $this->page('sysshop/license.html', $pagedata);
    }

    public function setProtocol()
    {
        $postdata = input::get('content');
        $setting = app::get('sysshop')->getConf('setprotocol');
        if($postdata)
        {
            $this->begin();
            app::get('sysshop')->setConf('setprotocol',$postdata);
            $this->end(true);
        }
        if($setting)
        {
            $pagedata['content'] = $setting;

        }
        return $this->page('sysshop/admin/enterapply/protocol.html', $pagedata);

    }

    /**
     * @brief 审核页面
     *
     * @param $enterapplyId
     *
     * @return html
     */
    public function doExamine($enterapplyId)
    {
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $list = $objMdlEnterapply->getRow('*',array('enterapply_id'=>$enterapplyId));
        $shop = unserialize($list['shop']);

        $checkBrand = array(
            'shop_type'=>$list['shop_type'],
            'shop'=>array('shop_brand'=>$shop['shop_brand']),
        );

        $list['seller_name'] = shopAuth::getSellerName($list['seller_id']);
        $list['shoptype'] = $this->shoptype[$list['shop_type']];

        if(is_array($shop['shop_cat']))
        {
            $catIds = implode(',',$shop['shop_cat']);
        }
        else
        {
            $catIds = $shop['shop_cat'];
        }
        $cat = app::get('sysshop')->rpcCall('category.cat.get.info',array('cat_id'=>$catIds));

        if($shop['shop_brand'])
        {
            $brand = app::get('sysshop')->rpcCall('category.brand.get.list',array('brand_id'=>$shop['shop_brand']))[$shop['shop_brand']];
        }

        if($list['new_brand'])
        {
            $brand = app::get('sysshop')->rpcCall('category.brand.get.list',array('brand_name'=>$list['new_brand']));
            if($brand) $brand = reset($brand);
        }

        try
        {
            $checkB = kernel::single('sysshop_data_enterapply')->checkBrand($checkBrand,$msg);
        }
        catch(\LogicException $e)
        {
            echo $brand['brand_name'].$e->getMessage();
            exit;
        }
        if(!$checkB) $pagedata['checkbrand'] = $msg;

        $shop['brand_name'] = $brand['brand_name'];
        if(is_array($shop['shop_cat']))
        {
            foreach($shop['shop_cat'] as $id)
            {
                $shop_cat .= $cat[$id]['cat_name'].",  ";
            }
        }
        else
        {
            $shop_cat = $cat['cat_name'];
        }
        $shop_cat = rtrim($shop_cat,',  ');
        $shop['shop_cat'] = $shop_cat;

        $shopinfo = unserialize($list['shop_info']);


        //图片数据反json序列化
        $array = ['license_img', 'trademark_img', 'inspection_report_img', 'barcode_certificate_img'];
        foreach ($array as $value) {
            $shopinfo[$value] = empty($shopinfo[$value]) ? '[]' : $shopinfo[$value];
            $shopinfo[$value] = json_decode($shopinfo[$value], true);

            if (!empty($shopinfo[$value])) {
                foreach ($shopinfo[$value] as $k => $val) {
                    $shopinfo[$value][$k] = base_storager::modifier($val);
                }
            }
        }

        // $shopinfo['corporate_identity_img'] = base_storager::modifier($shopinfo['corporate_identity_img'] );
        // $shopinfo['corporate_identity_img_z'] = base_storager::modifier($shopinfo['corporate_identity_img_z'] );
        // $shopinfo['corporate_identity_img_f'] = base_storager::modifier($shopinfo['corporate_identity_img_f'] );
        // $shopinfo['tissue_code_img'] = base_storager::modifier($shopinfo['tissue_code_img']);
        // $shopinfo['tax_code_img'] = base_storager::modifier($shopinfo['tax_code_img']);
        // $shopinfo['shopuser_identity_img_z'] = base_storager::modifier($shopinfo['shopuser_identity_img_z']);
        // $shopinfo['shopuser_identity_img_f'] = base_storager::modifier($shopinfo['shopuser_identity_img_f']);
        // var_dump($shopinfo['license_img']);exit;
        // $shopinfo['license_img'] = json_decode($shopinfo['license_img']);
        // $shopinfo['license_img'] = base_storager::modifier($shopinfo['license_img']);
        // $shopinfo['brand_warranty'] = base_storager::modifier($shopinfo['brand_warranty']);

        $pagedata['shop'] =$shop;
        $pagedata['shop_info'] = $shopinfo;
        $pagedata['itemdata'] = $list;
        return $this->page('sysshop/admin/enterapply/examine.html', $pagedata);
    }

    /**
     * @brief 列表tab
     *
     * @return
     */
    public function _views()
    {

        $subMenu = array(
            0=>array(
                'label'=>app::get('sysshop')->_('待审核'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'active',
                ),
            ),
            1=>array(
                'label'=>app::get('sysshop')->_('审核通过'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'successful',
                ),

            ),
            2=>array(
                'label'=>app::get('sysshop')->_('审核驳回'),
                'optional'=>false,
                'filter'=>array(
                    'status'=>'failing',
                ),
            ),
            3=>array(
                'label'=>app::get('sysshop')->_('全部'),
                'optional'=>false,
            ),
        );
        return $subMenu;
    }

    /**
     * @brief 审核保存
     *
     * @return
     */
    public function goExamine()
    {
        $postdata = $_POST ;
        $this->begin();
        try{
            $objEnterapply = kernel::single('sysshop_data_enterapply');
            $result = $objEnterapply->consentRepulse($postdata);
        }
        catch(\LogicException $e)
        {
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $url_param = array('app'=>'sysshop', 'ctl'=>'admin_enterapply', 'act'=>'index');
        $this->end(true,$msg, $url_param);
    }

    /**
     * @brief 店铺开启
     *
     * @param $enterapplyId int
     *
     * @return
     */
    public function openShop($enterapplyId)
    {
        $this->begin('?app=sysshop&ctl=admin_enterapply&act=index');
        try
        {
            $this->adminlog('开启店铺[enterapplyId:{$enterapplyId}]', 1);
            $objShop = kernel::single('sysshop_data_shop');
            $result = $objShop->openShop($enterapplyId);
        }
        catch(\LogicException $e)
        {
            $this->adminlog('开启店铺[enterapplyId:{$enterapplyId}]', 0);
            $msg = $e->getMessage();
            $this->end(false,$msg);
        }
        $this->end(true,"店铺开通成功");
    }

    /**
     * @brief 关联商家新增的品牌 list
     *
     * @param $id
     *
     * @return
     */
    public function doRelevance($id)
    {

        try{
            $objMdlEnterapply = app::get('sysshop')->model('enterapply');
            $list = $objMdlEnterapply->getRow('enterapply_id,shop,new_brand,shop_name,shop_type',array('enterapply_id'=>$id));
            $shop = unserialize($list['shop']);
            unset($list['shop']);

            if(count($shop['shop_cat']) ==1)
            {
                $catId = $shop['shop_cat'][0];
            }
            else
            {
                $catId = implode(',',$shop['shop_cat']);
            }
            $cat = app::get('sysshop')->rpcCall('category.cat.get.info',array('cat_id'=>$catId,'fields'=>'cat_id,cat_name'));
            $cat = array_shift($cat);

            $brand = app::get('sysshop')->rpcCall('category.brand.get.list',array('brand_name'=>$list['new_brand'],'fields'=>'brand_id,brand_name'));
            $brand = array_shift($brand);
            $isrel = 0;

            if($brand)
            {
                $brandlist = app::get('sysshop')->rpcCall('category.get.cat.rel.brand',array('cat_id'=>$cat['cat_id']));

                if($brandlist)
                {
                    foreach($brandlist as $key=>$val)
                    {
                        if($val['brand_id'] == $brand['brand_id'])
                        {
                            $isrel = 1;
                        }
                    }
                }
            }
        }
        catch(\LogicException $e)
        {
            //echo $e->getMessage();
        }

        $pagedata['isrel'] = $isrel;
        $pagedata['brand'] = $brand;
        $pagedata['new_brand'] = $list['new_brand'];
        $pagedata['cat'] = $cat;
        $pagedata['shop'] = array_merge($list,$shop);

        return $this->page('sysshop/admin/enterapply/relevance.html', $pagedata);
    }

    /**
     * @brief 关联商家新增的品牌 save
     *
     * @return
     */
    public function goRelevance()
    {
        $this->begin();
        $postdata = $_POST['shop'];
        $objMdlEnterapply = app::get('sysshop')->model('enterapply');
        $list = $objMdlEnterapply->getRow('enterapply_id,shop,new_brand',array('enterapply_id'=>$postdata['enterapply_id']));
        $list['shop'] = unserialize($list['shop']);
        $list['shop']['shop_brand'] = $postdata['shop_brand'];
        //$list['shop']['shop_cat'] = $postdata['shop_cat'];
        $result = $objMdlEnterapply->save($list);
        $this->adminlog("关联商家新增的品牌[enterapply_id:{$list['enterapply_id']}]", 1);
        $this->end();
    }

    /**
     * @brief 跳转至品牌列表
     *
     * @return
     */
    public function goBrand(){
        $this->begin('?app=syscategory&ctl=admin_brand&act=index');
        $this->end(true);

    }

}



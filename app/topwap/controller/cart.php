<?php
class topwap_ctl_cart extends topwap_controller
{

    public function addBatchCart()
    {
        $mode = input::get('mode');
        $obj_type = input::get('obj_type');
        $itemsData = input::get('itemData');

        foreach($itemsData as $key => $val){
            $params['obj_type'] = $obj_type ? $obj_type : 'item';
            $params['mode'] = $mode ? $mode : 'cart';
            $params['user_id'] = userAuth::id();

            // if( $params['obj_type']=='package' )
            // {
            //     $package_id = input::get('package_id');
            //     $params['package_id'] = intval($package_id);
            //     $skuids = input::get('package_item');
            //     $tmpskuids = array_column($skuids, 'sku_id');
            //     $params['package_sku_ids'] = implode(',', $tmpskuids);
            //     $params['quantity'] = input::get('package-item.quantity',1);
            // }
            if( $params['obj_type']=='item')
            {
                $quantity = $val['quantity'];
                $params['quantity'] = $quantity ? $quantity : 1; //购买数量，如果已有购买则累加
                $params['sku_id'] = intval($val['sku_id']);
            }

            try
            {
                $data = kernel::single('topwap_cart')->addCart($params);
                if( $data === false )
                {
                    $msg = app::get('topwap')->_('加入购物车失败!');
                    return $this->splash('error',null,$msg,true);
                }
            } catch(Exception $e) {
                $msg = $e->getMessage();
                return $this->splash('error',null,$msg,true);
            }
        }
    
        $msg = app::get('topwap')->_('成功加入购物车');
        return $this->splash('success',$url,$msg,true);
        
    }

    public function addCart()
    {
        $mode = input::get('mode');
        $obj_type = input::get('obj_type');

        $params['obj_type'] = $obj_type ? $obj_type : 'item';
        $params['mode'] = $mode ? $mode : 'cart';
        $params['user_id'] = userAuth::id();
        if( $params['obj_type']=='package' )
        {
            $package_id = input::get('package_id');
            $params['package_id'] = intval($package_id);
            $skuids = input::get('package_item');
            $tmpskuids = array_column($skuids, 'sku_id');
            $params['package_sku_ids'] = implode(',', $tmpskuids);
            $params['quantity'] = input::get('package-item.quantity',1);
        }
        if( $params['obj_type']=='item')
        {
            $quantity = input::get('item.quantity');
            $params['quantity'] = $quantity ? $quantity : 1; //购买数量，如果已有购买则累加
            $params['sku_id'] = intval(input::get('item.sku_id'));
        }

        try
        {
            $data = kernel::single('topwap_cart')->addCart($params);
            if( $data === false )
            {
                $msg = app::get('topwap')->_('加入购物车失败!');
                return $this->splash('error',null,$msg,true);
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        $msg = app::get('topwap')->_('成功加入购物车');
        $url = "";
        if( $params['mode'] == 'fastbuy' )
        {
            $url = url::action('topwap_ctl_cart_checkout@index',array('mode'=>'fastbuy') );
            $msg = "";
        }
        return $this->splash('success',$url,$msg,true);
    }

    public function index()
    {
        $this->setLayoutFlag('cart');

        $pagedata['nologin'] = userAuth::check() ? false : true;
        $pagedata['defaultImageId'] = kernel::single('image_data_image')->getImageSetting('item');

        $cartData = kernel::single('topwap_cart')->getCartInfo();

        $pagedata['aCart'] = $cartData['resultCartData'];
        $pagedata['totalCart'] = $cartData['totalCart'];
    
        // 店铺可领取优惠券
        foreach ($pagedata['aCart'] as &$v) {
            $params = array(
                'page_no' => 0,
                'page_size' => 1,
                'fields' => '*',
                'shop_id' => $v['shop_id'],
                'platform' => 'wap',
                'is_cansend' => 1,
            );
            $couponListData = app::get('topwap')->rpcCall('promotion.coupon.list', $params, 'buyer');
            if($couponListData['count']>0)
            {
                $v['hasCoupon'] = 1;
            }
        }

        $pagedata['showPay'] = 1;

        //页面改版，商品信息返回二维数组
        $pagedata['cartInfoBySkuId'] = $this->__returnCartInfoBySkuId($pagedata['aCart'], $pagedata['showPay']);

        // var_dump($pagedata);exit;
        // return $this->page('topwap/cart/index.html',$pagedata);
        //v20200324版本
        return $this->page('topwap/cart/index_0324.html', $pagedata);
    }

    /**
     * 页面改版，商品信息返回二维数组，按sku_id分类
     *
     * @param [type] $cartInfo  商品信息一维数组
     * @return void
     */
    private function __returnCartInfoBySkuId($cartInfo, &$showPay)
    {
        // return $cartInfo;
        if (empty($cartInfo)) return $cartInfo;

        $returnArray = [];

        $isCheck = 0;
        foreach ($cartInfo as $shopId => $shopInfo)
        {
            $returnArray[$shopId] = $shopInfo;

            //spu总数
            $spuCount = 0;
            //sku总数
            $skuCount = 0;

            //获取购物车的数组
            $cartArray = isset($shopInfo['object']) ? $shopInfo['object'] : [];
            if (empty($cartArray)) continue;

            $tempArray = [];
            foreach ($cartArray as $cartRecordId => $cart)
            {
                $goodsItemId = $cart['item_id'];

                if ($cart['valid']) {
                    $skuCount    += $cart['quantity'];
                }

                //保存商品信息
                if (!isset($tempArray[$goodsItemId])) $tempArray[$goodsItemId] = $this->__getGoodsItemInfo($goodsItemId, $cart);
                
                //获取sku_id
                $skuId = $cart['sku_id'];
                //获取购物车信息
                $tempArray[$goodsItemId]['cart_item_list'][$cartRecordId] = $this->__getGoodsCartInfo($cart);
            }

            //判断是否可以支付
            $flag = 1;
            foreach ($tempArray as $tempKey => $cart) {
                if (empty($cart['valid'])) {
                    continue;
                }

                $spuCount += 1;
                $miniQuantity = $cart['mini_quantity'];
                $quantity = 0;
                $tempChecked = 0;
                foreach ($cart['cart_item_list'] as $cartItem) {
                    if ($cartItem['is_checked']) {
                        $quantity += $cartItem['quantity'];
                        $tempChecked = 1;
                        $isCheck = 1;
                    }
                }

                $tempArray[$tempKey]['totalQuantity'] = $quantity;

                if ($quantity && $tempChecked && $miniQuantity > $quantity && !empty($showPay)) {
                    $showPay = 0;
                }
               
                $flag++;
            }

            $returnArray[$shopId]['object'] = $tempArray;

            $returnArray[$shopId]['skuCount'] = $skuCount;
            $returnArray[$shopId]['spuCount'] = $spuCount;
        }

        if (empty($isCheck)) {
            $showPay = 0;
        }

        return $returnArray;
    }

    /**
     * 获取商品信息
     *
     * @param [type] $goodsItemId  商品id
     * @param [type] $cart         购物车信息
     * @return void
     */
    private function __getGoodsItemInfo($goodsItemId, $cart)
    {
        $returnArray = [];
        $dlytmplInfo = [];

        if ($cart['dlytmpl_id'])
        {
           //获取是否免邮的信息
            $dlytmplParams = [
                'template_id' => $cart['dlytmpl_id'],
                'fields' => 'is_free'
            ];
            $dlytmplInfo = app::get('topwap')->rpcCall('logistics.dlytmpl.get', $dlytmplParams);
        }
        
        $returnArray['freeConf'] = empty($dlytmplInfo) ? 0 : (int) $dlytmplInfo['is_free'];
        
        $needDeleteKeyArray = ['price', 'quantity', 'weight', 'step_price', 'step_price_cur'];
        foreach ($cart as $key => $value)
        {
            // if (!in_array($key, $needDeleteKeyArray)) {
                $returnArray[$key] = $value;
            // }
        }

        return $returnArray;
    }

    /**
     * 获取购物车信息
     *
     * @param [type] $cart
     * @return void
     */
    private function __getGoodsCartInfo($cart)
    {
        return $cart;
    }

    public function updateCart()
    {
        $postCartId = input::get('cart_id');
        $postCartNum = input::get('cart_num');
        $postPromotionId = input::get('promotionid');
        $params = array();
        foreach ($postCartId as $cartId => $v)
        {
            $data['mode'] = $mode;
            $data['obj_type'] = $obj_type;
            $data['cart_id'] = intval($cartId);
            $data['totalQuantity'] = intval($postCartNum[$cartId]);
            $data['selected_promotion'] = intval($postPromotionId[$cartId]);
            $data['user_id'] = userAuth::id();

            if($v=='1')
            {
                $data['is_checked'] = '1';
            }
            if($v=='0')
            {
                $data['is_checked'] = '0';
            }
            $params[] = $data;
        }

        try
        {
            foreach($params as $updateParams)
            {
                //$data = app::get('topwap')->rpcCall('trade.cart.update',$updateParams);
                $data = kernel::single('topwap_cart')->updateCart($updateParams);
                if( $data === false )
                {
                    $msg = app::get('topwap')->_('更新失败');
                    return $this->splash('error',null,$msg,true);
                }
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }

        $cartData = kernel::single('topwap_cart')->getCartInfo();
        $pagedata['aCart'] = $cartData['resultCartData'];

        // 临时统计购物车页总价数量等信息
        $totalWeight = 0;
        $totalNumber = 0;
        $totalPrice = 0;
        $totalDiscount = 0;
        foreach($cartData['resultCartData'] as $v)
        {
            $totalWeight += $v['cartCount']['total_weight'];
            $totalNumber += $v['cartCount']['itemnum'];
            $totalPrice += $v['cartCount']['total_fee'];
            $totalDiscount += $v['cartCount']['total_discount'];
        }
        $totalCart['totalWeight'] = $totalWeight;
        $totalCart['number'] = $totalNumber;
        $totalCart['totalPrice'] = $totalPrice;
        $totalCart['totalAfterDiscount'] = ecmath::number_minus(array($totalPrice, $totalDiscount));
        $totalCart['totalDiscount'] = $totalDiscount;
        $pagedata['totalCart'] = $totalCart;

        $pagedata['defaultImageId'] = kernel::single('image_data_image')->getImageSetting('item');

        foreach(input::get('cart_shop') as $shopId => $cartShopChecked)
        {
            $pagedata['selectShop'][$shopId] = $cartShopChecked=='on' ? true : false;
        }
        $pagedata['selectAll'] = input::get('cart_all')=='on' ? true : false;

        // 店铺可领取优惠券
        foreach ($pagedata['aCart'] as &$v) {
            $params = array(
                'page_no' => 0,
                'page_size' => 1,
                'fields' => '*',
                'shop_id' => $v['shop_id'],
                'platform' => 'wap',
                'is_cansend' => 1,
            );
            $couponListData = app::get('topwap')->rpcCall('promotion.coupon.list', $params, 'buyer');
            if($couponListData['count']>0)
            {
                $v['hasCoupon'] = 1;
            }
        }

        $pagedata['nologin'] = userAuth::check() ? false : true;


        $pagedata['isUpdate'] = 1;
        $pagedata['showPay'] = 1;

        //v20200324版本
        $pagedata['cartInfoBySkuId'] = $this->__returnCartInfoBySkuId($pagedata['aCart'], $pagedata['showPay']);
        $msg = view::make('topwap/cart/cart_main_0324.html', $pagedata)->render();
        // $msg = view::make('topwap/cart/cart_main.html', $pagedata)->render();

        return $this->splash('success',null,$msg,true);
    }

    public function ajaxGetItemPromotion()
    {
        $itemId = intval(input::get('item_id'));
        $skuId = intval(input::get('sku_id'));
        $platform='wap';

        $itemPromotionTagInfo = app::get('systrade')->rpcCall('item.promotion.get', array('item_id'=>$itemId),'buyer');
        if(!$itemPromotionTagInfo)
        {
            return false;
        }
        $allPromotion = array();
        foreach($itemPromotionTagInfo as $v)
        {
            $basicPromotionInfo = app::get('systrade')->rpcCall('promotion.promotion.get', array('promotion_id'=>$v['promotion_id'], 'platform'=>$platform), 'buyer');
            if( $v['sku_id'] && !in_array($skuId,explode(',',$v['sku_id'])) )
            {
                continue;
            }

            if($basicPromotionInfo['valid']===true)
            {
                $allPromotion[$v['promotion_id']] = $basicPromotionInfo;
            }
        }
        // 倒序排序，购物车的默认促销规则选择最新添加的促销适用
        ksort($allPromotion);
        $pagedata['promotions'] = $allPromotion;
        return view::make('topwap/cart/item_promotion.html',$pagedata);
    }

    /**
     * @brief 删除购物车中数据
     *
     * @return
     */
    public function removeCart()
    {
        $postCartIdsData = input::get('cart_id');
        $tmpCartIds['cart_id'] = array_filter(explode(',',$postCartIdsData));
        $params['cart_id'] = implode(',',$tmpCartIds['cart_id']);
        if(!$params['cart_id'])
        {
            return $this->splash('error',null,'请选择需要删除的商品！',true);
        }
        $params['user_id'] = userAuth::id();

        try
        {
            $res = kernel::single('topwap_cart')->deleteCart($params);
            if( $res === false )
            {
                throw new Exception(app::get('topwap')->_('删除失败'));
            }
        }
        catch(Exception $e)
        {
            $msg = $e->getMessage();
            return $this->splash('error',null,$msg,true);
        }
        return $this->splash('success',null,'删除成功',true);
    }

    public function ajaxBasicCart()
    {
        $cartData = kernel::single('topwap_cart')->getCartInfo();
        $pagedata['aCart'] = $cartData['resultCartData'];

        $pagedata['totalCart'] = $cartData['totalCart'];

        $pagedata['defaultImageId'] = kernel::single('image_data_image')->getImageSetting('item');

        foreach(input::get('cart_shop') as $shopId => $cartShopChecked)
        {
            $pagedata['selectShop'][$shopId] = $cartShopChecked=='on' ? true : false;
        }
        $pagedata['selectAll'] = input::get('cart_all')=='on' ? true : false;

        // $msg = view::make('topwap/cart/cart_main.html', $pagedata)->render();
        //v20200324版本
        $pagedata['isUpdate'] = 1;
        $pagedata['showPay'] = 1;

        $pagedata['cartInfoBySkuId'] = $this->__returnCartInfoBySkuId($pagedata['aCart'], $pagedata['showPay']);
        $msg = view::make('topwap/cart/cart_main_0324.html', $pagedata)->render();
        return $this->splash('success',null,$msg,true);
    }
}

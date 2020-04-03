
<?php
class topapi_utils_checkfields{

    var $colsWhiteList;
    var $tableWhiteList;

    public function __construct()
    {
        $this->colsWhiteList = $this->genWhiteList();
        $this->tableWhiteList = $this->genTableWhiteList();
    }

    public function checkfields($value)
    {
        if(empty($value)) return true;
        $this->removeAs($value);
        $vs = explode(',', $value);
        foreach($vs as $v)
        {
            $this->checkfield($v);
        }
        return true;
    }

    public function checkfield($col)
    {
        $col = trim($col);
        $this->checkcolchar($col);

        //table white list maybe have some problem
        //col white list need cache
        //so, default off
        if( !config::get('security.topapi.fieldsWhiteList', false)) return true;

        $colArr = explode('.', $col);
        if(count($colArr) > 1)
        {
            $table = $colArr[0];
            if(!in_array($table, $this->tableWhiteList))
            {
                throw new RuntimeException('table is not in white list');
            }
            $extCol = $colArr[1];
        }else{
            $extCol = $colArr[0];
        }
        if($extCol == '*') return true;
        if(!in_array($extCol, $this->colsWhiteList))
        {
            throw new RuntimeException('col is not in white list');
        }
    }

    public function checkcolchar($col)
    {
        $c = preg_match("/^([*.a-zA-Z0-9_-]{1,100})$/", $col);
        if(!$c)
        {
            throw new RuntimeException('col looks not good');
        }
        return true;
    }

    public function removeAs($fieds)
    {

        return true;
    }

    public function genTableWhiteList()
    {
        return [
            'base_app_content', 'base_apps', 'base_crontab', 'base_kvstore', 'base_network', 'base_rpcpoll', 'base_setting', 'base_syscache_resources', 'desktop_account', 'desktop_filter', 'desktop_hasrole', 'desktop_menus', 'desktop_roles', 'desktop_tag', 'desktop_tag_rel', 'desktop_users', 'ectools_payments', 'ectools_refunds', 'ectools_trade_paybill', 'image_image_cat', 'image_images', 'importexport_task', 'search_associate', 'search_delta', 'site_explorers',
            'site_link', 'site_menus', 'site_seo', 'site_themes', 'site_themes_file', 'site_themes_tmpl', 'site_widgets', 'site_widgets_instance', 'sysaftersales_aftersales', 'sysaftersales_refunds', 'sysaftersales_setting', 'sysapp_clients', 'sysapp_message_log', 'sysapp_widgets_instance', 'syscategory_brand', 'syscategory_cat', 'syscategory_cat_rel_brand', 'syscategory_cat_rel_prop', 'syscategory_prop_values', 'syscategory_props', 'syscategory_virtualcat',
            'sysclearing_vouchersubsidy_detail', 'syscontent_article', 'syscontent_article_nodes', 'syscontent_article_shop', 'syscontent_article_shop_nodes', 'sysdecorate_widgets_instance', 'sysfinance_guaranteeMoney', 'sysfinance_guaranteeMoney_oplog', 'sysim_account_webcall_shop', 'sysitem_item', 'sysitem_item_count', 'sysitem_item_desc', 'sysitem_item_nature_props', 'sysitem_item_promotion', 'sysitem_item_search_rule', 'sysitem_item_search_shopweight',
            'sysitem_item_search_weight', 'sysitem_item_status', 'sysitem_item_store', 'sysitem_sku', 'sysitem_sku_store', 'sysclearing_settlement', 'sysclearing_settlement_detail', 'sysclearing_vouchersubsidy',
            'sysitem_spec_index', 'syslogistics_delivery', 'syslogistics_delivery_detail', 'syslogistics_dlycorp', 'syslogistics_dlytmpl', 'syslogistics_ziti', 'sysopen_develop', 'sysopen_keys', 'sysopen_platform_bind', 'sysopen_shopconf', 'sysopen_shopexMatrix', 'sysopen_shopexProduct', 'syspromotion_activity', 'syspromotion_activity_item', 'syspromotion_activity_register', 'syspromotion_coupon', 'syspromotion_coupon_item', 'syspromotion_distribute',
            'syspromotion_distribute_detail', 'syspromotion_freepostage', 'syspromotion_freepostage_item', 'syspromotion_fulldiscount', 'syspromotion_fulldiscount_item', 'syspromotion_fullminus', 'syspromotion_fullminus_item', 'syspromotion_gift', 'syspromotion_gift_item', 'syspromotion_gift_sku', 'syspromotion_hongbao', 'syspromotion_lottery', 'syspromotion_lottery_result', 'syspromotion_package', 'syspromotion_package_item', 'syspromotion_page',
            'syspromotion_page_tmpl', 'syspromotion_promotions', 'syspromotion_remind', 'syspromotion_scratchcard', 'syspromotion_scratchcard_result', 'syspromotion_voucher', 'syspromotion_voucher_register', 'syspromotion_xydiscount', 'syspromotion_xydiscount_item', 'sysrate_appeal', 'sysrate_append', 'sysrate_consultation', 'sysrate_dsr', 'sysrate_feedback', 'sysrate_score', 'sysrate_traderate', 'sysshop_account', 'sysshop_enterapply', 'sysshop_roles',
            'sysshop_seller', 'sysshop_sellerloginlog', 'sysshop_shop', 'sysshop_shop_apply_cat', 'sysshop_shop_cat',
            'sysshop_shop_info', 'sysshop_shop_notice', 'sysshop_shop_rel_brand', 'sysshop_shop_rel_dlycorp', 'sysshop_shop_rel_lv1cat', 'sysshop_shop_rel_seller', 'sysshop_shop_type', 'sysshop_store_police', 'sysshop_subdomain', 'sysstat_desktop_collect_item', 'sysstat_desktop_collect_shop', 'sysstat_desktop_item_statics', 'sysstat_desktop_stat_shop', 'sysstat_desktop_stat_user', 'sysstat_desktop_stat_userorder', 'sysstat_desktop_trade_statics',
            'sysstat_item_statics', 'sysstat_statmember', 'sysstat_trade_statics', 'system_adminlog', 'system_adminloginlog', 'system_apilog', 'system_file', 'system_matrixset', 'system_messenger_systmpl', 'system_prism_initstep', 'system_queue_failed', 'system_queue_mysql', 'system_seller_log', 'system_smslog', 'systrade_activity_detail', 'systrade_cart', 'systrade_cart_coupon', 'systrade_cart_item', 'systrade_delivery_code', 'systrade_log', 'systrade_order',
            'systrade_order_complaints', 'systrade_promotion_detail', 'systrade_trade', 'systrade_trade_cancel', 'sysuser_account', 'sysuser_shop_fav', 'sysuser_tag', 'sysuser_trustinfo', 'sysuser_user', 'sysuser_user_addrs', 'sysuser_user_checkin', 'sysuser_user_coupon', 'sysuser_user_crm', 'sysuser_user_deposit', 'sysuser_user_deposit_cash', 'sysuser_user_deposit_log', 'sysuser_user_experience', 'sysuser_user_fav', 'sysuser_user_grade', 'sysuser_user_hongbao',
            'sysuser_user_hongbao_tmp', 'sysuser_user_item_notify', 'sysuser_user_login_log', 'sysuser_user_point', 'sysuser_user_pointlog', 'sysuser_user_points', 'sysuser_user_trade_count', 'sysuser_user_voucher',
        ];
    }

    public function genWhiteList()
    {
        $appIds = $this->getAppIds();
        $cc = [];
        foreach ($appIds as $appId)
        {
            $tablesOutline = [];
            foreach(kernel::single('base_application_dbtable')->detect($appId) as $item)
            {
                $dbtable = $item->load();
                foreach($dbtable['columns'] as $k => $v)
                {
                    $cc[] = $k;
                }
            }
        }
    }

    public function getAppIds()
    {
        $d = dir(APP_DIR);
        while (false !== ($entry = $d->read())) {
            if ($entry!='.' && $entry!='..') {
                if (is_dir(APP_DIR.'/'.$entry)) {
                    $apps[] = $entry;
                }
            }
        }
        $d->close();
        return $apps;
    }


}

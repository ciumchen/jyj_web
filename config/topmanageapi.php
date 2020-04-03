
<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    //  'region'=>'地区',
    //  'logistics'=>'物流',
    //  'theme'=>'模板',
    //  'category'=>'品牌类目',
    //  'user'=>'账户',
    //  'member'=>'会员',
    //  'trade'=>'交易',
    //  'item'=>'商品',
    //  'cart'=>'购物车',
    //  'promotion'=>'促销',
    //  'payment'=>'支付',
    //  'shop'=>'店铺',
    //  'content'=>'内容管理',
    //  'common'=>'通用',
    //  'image'=>'图片',
    //  'message'=>'消息',

    /*
    |--------------------------------------------------------------------------
    | 定义所有topmanageapi api接口路由
    |--------------------------------------------------------------------------
    | v1 表示API版本号
    | theme.modules API调用方法
    | topapi_api_v1_theme_modules API实现类默认调用handle方法
     */
    'routes' => array(
        'v1' => [
            /**
             *  通用类
             **/
            //v1示例1 无需身份验证
            'common.example' => ['uses' => 'topmanageapi_api_v1_example'],
            //v1示例2 需要身份验证
            'common.example2' => ['uses' => 'topmanageapi_api_v1_example2', 'auth'=>true],

            /**
             *  账户相关
             **/
            //会员登录
            'user.login' => ['uses' => 'topmanageapi_api_v1_account_login'],
            'user.logout' => ['uses' => 'topmanageapi_api_v1_account_logout','auth'=>true],
            //验证手机号码
            'user.checkmobile'=> ['uses' => 'topmanageapi_api_v1_account_checkMobile'],
            //发送短信`
            'user.send'=> ['uses' => 'topmanageapi_api_v1_account_send'],
            //找回密码重置密码
            'user.updatePasswd'=> ['uses' => 'topmanageapi_api_v1_account_updatePasswd'],

            /**
             *  商品相关
             **/
            //商品列表
            'item.list' => ['uses' => 'topmanageapi_api_v1_item_itemList','auth'=>true],
            //新增、编辑商品保存
            'item.save'   => ['uses' => 'topmanageapi_api_v1_item_create','auth'=>true],
            //新增、编辑商品保存
            'item.create' => ['uses' => 'topmanageapi_api_v1_item_create','auth'=>true],
            //获取商品详情
            'item.detail' => ['uses' => 'topmanageapi_api_v1_item_itemDetail','auth'=>true],
            //商品上下架状态
            'item.status' => ['uses' => 'topmanageapi_api_v1_item_itemStatus','auth'=>true],
            //删除商品
            'item.delete' => ['uses' => 'topmanageapi_api_v1_item_itemDelete','auth'=>true],
            //设置库存报警
            'item.savestorepolice' => ['uses' => 'topmanageapi_api_v1_item_saveStorePolice','auth'=>true],
            //获取库存报警信息
            'item.getstorepolice' => ['uses' => 'topmanageapi_api_v1_item_getStorePolice','auth'=>true],

            /**
             *  订单相关
             **/
            //订单列表
            'trade.list' => ['uses' => 'topmanageapi_api_v1_trade_list','auth'=>true],
            //订单详情
            'trade.info' => ['uses' => 'topmanageapi_api_v1_trade_info','auth'=>true],
            //订单发货
            'trade.delivery' => ['uses' => 'topmanageapi_api_v1_trade_delivery','auth'=>true],

            /**
             *  数据统计
             **/
            //首页数据——UV，代付款数据
            'analysis.index.countdata' => ['uses' => 'topmanageapi_api_v1_index_countData' , 'auth'=>true],
            //首页数据——昨日销售商品排行
            'analysis.index.goodsoldlist' => ['uses' => 'topmanageapi_api_v1_index_goodSoldList' , 'auth'=>true],
            //首页数据——每个时间点的销售数据
            'analysis.index.timeData' => ['uses' => 'topmanageapi_api_v1_index_timeData' , 'auth'=>true],

            /**
             *  安全中心
             **/
            //验证登录密码
            'security.checkpassword' => ['uses' => 'topmanageapi_api_v1_security_checkPassword', 'auth'=>true],
            //修改认证信息
            'security.updateAuth' => ['uses' => 'topmanageapi_api_v1_security_updateAuth', 'auth'=>true],
            //获取卖家信息
            'security.getSellerData' => ['uses' => 'topmanageapi_api_v1_security_getSellerData', 'auth'=>true],

            /**
             *  验证码
             **/
            //生成验证码
            'vcode.genvcode' => ['uses' => 'topmanageapi_api_v1_vcode_genVcode'],
            //发送验证码
            'vcode.sendvcode' => ['uses' => 'topmanageapi_api_v1_vcode_sendVcode','auth'=>true],

            /**
             *  文章相关
             **/
            //文章列表
            'article.list' => ['uses' => 'topmanageapi_api_v1_article_list','auth'=>true],
            //新增、修改文章
            'article.save' => ['uses' => 'topmanageapi_api_v1_article_save','auth'=>true],
            //删除文章
            'article.del' => ['uses' => 'topmanageapi_api_v1_article_del','auth'=>true],
            //读取文章
            'article.get' => ['uses' => 'topmanageapi_api_v1_article_get','auth'=>true],
            //获取文章分类列表
            'article.getnodes' => ['uses' => 'topmanageapi_api_v1_article_getNodes','auth'=>true],
            //保存文章分类
            'article.savenode' => ['uses' => 'topmanageapi_api_v1_article_saveNode','auth'=>true],
            //删除文章分类
            'article.delnode' => ['uses' => 'topmanageapi_api_v1_article_delNode','auth'=>true],

            /**
             *  意见反馈
             **/
            //回复意见反馈
            'advice.shop.addFeedback' => ['uses' => 'topmanageapi_api_v1_addFeedback','auth'=>true],

            /**
             * 报表相关
             */
            //商家运营概况
            'statement.shop.opera.info' => ['uses' => 'topmanageapi_api_v1_statement_operaInfo','auth'=>true],
            //商家运营概况报表数据
            'statement.shop.opera.info.chart' => ['uses' => 'topmanageapi_api_v1_statement_operaInfochart','auth'=>true],
            //交易数据分析
            'statement.shop.trade.analyze' => ['uses' => 'topmanageapi_api_v1_statement_tradeAnalyze','auth'=>true],
            'statement.shop.trade.analyze.chart' => ['uses' => 'topmanageapi_api_v1_statement_tradeAnalyzechart','auth'=>true],
            //商品销售分析
            'statement.shop.item.analyze' => ['uses' => 'topmanageapi_api_v1_statement_itemAnalyze','auth'=>true],
            'statement.shop.item.analyze.chart' => ['uses' => 'topmanageapi_api_v1_statement_itemAnalyzechart','auth'=>true],
            //问题订单分析
            'statement.shop.order.aftersale.analyze' => ['uses' => 'topmanageapi_api_v1_statement_orderAftersaleAnalyze','auth'=>true],
            'statement.shop.order.aftersale.analyze.chart' => ['uses' => 'topmanageapi_api_v1_statement_orderAftersaleAnalyzechart','auth'=>true],
            //流量数据分析
            'statement.shop.uv.analyze' => ['uses' => 'topmanageapi_api_v1_statement_uvAnalyze','auth'=>true],
            'statement.shop.uv.analyze.chart' => ['uses' => 'topmanageapi_api_v1_statement_uvAnalyzechart','auth'=>true],
            /**
             *  品牌类目相关
             **/
            //获取平台分类列表
            'category.platform.get' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategory','auth'=>true],
            //根据分类id获取子节点分类
            'category.platform.get.parent' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategoryParent','auth'=>true],
            //分类自然属性列表获取
            'category.platform.natureprops.get' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategoryNatureProps','auth'=>true],
            //分类品牌列表获取
            'category.platform.brand.get' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategoryBrand','auth'=>true],
            //分类属性获取
            'category.platform.props' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategoryProps','auth'=>true],
            //分类参数获取
            'category.platform.params' => ['uses' => 'topmanageapi_api_v1_category_getPlatformCategoryParams','auth'=>true],
            //店铺分类列表获取
            'category.shop.get' => ['uses' => 'topmanageapi_api_v1_category_getShopCategory','auth'=>true],

            /**
             *  运费模板相关
             **/
            //运费模板列表
            'dlytmpl.shop.list' => ['uses' => 'topmanageapi_api_v1_dlytmpl_list','auth'=>true],
            //运费模板信息
            'dlytmpl.shop.row' => ['uses' => 'topmanageapi_api_v1_dlytmpl_row','auth'=>true],
            //运费模板对应的快递方式列表
            'dlytmpl.dlycorp.shop.list' => ['uses' => 'topmanageapi_api_v1_dlytmpl_corpList','auth'=>true],

            /**
             *  店铺配置相关
             **/
            //读取店铺配置
            'shop.main.setting.read' => ['uses' => 'topmanageapi_api_v1_shop_readSetting','auth'=>true],
            //保存店铺配置
            'shop.main.setting.write' => ['uses' => 'topmanageapi_api_v1_shop_writeSetting','auth'=>true],

            /**
             *  客服相关
             */
            //退换货列表
            'customer.aftersale.list' => ['uses' => 'topmanageapi_api_v1_customer_afterSalelist','auth'=>true],
            //退换货详情
            'customer.aftersale.info' => ['uses' => 'topmanageapi_api_v1_customer_afterSaleinfo','auth'=>true],
            //审核售后申请
            'customer.aftersale.verification' => ['uses' => 'topmanageapi_api_v1_customer_afterSaleverification','auth'=>true],
            //审核换货填写物流信息
            'customer.aftersale.send' => ['uses' => 'topmanageapi_api_v1_customer_afterSaleSend','auth'=>true],
            //评价列表
            'customer.comment.list' => ['uses' => 'topmanageapi_api_v1_customer_commentList','auth'=>true],
            //评价详情
            'customer.comment.info' => ['uses' => 'topmanageapi_api_v1_customer_commentInfo','auth'=>true],
            //评价概况
            'customer.comment.count' => ['uses' => 'topmanageapi_api_v1_customer_commentCount','auth'=>true],
            //回复评价
            'customer.comment.reply' => ['uses' => 'topmanageapi_api_v1_customer_commentReply','auth'=>true],
            //申诉列表
            'customer.appeal.list' => ['uses' => 'topmanageapi_api_v1_customer_appealList','auth'=>true],
            //申诉详情
            'customer.appeal.info' => ['uses' => 'topmanageapi_api_v1_customer_appealInfo','auth'=>true],
            //评价申诉 二次申诉
            'customer.appeal.again' => ['uses' => 'topmanageapi_api_v1_customer_appealAgain','auth'=>true],
            //咨询列表
            'customer.consult.list' => ['uses' => 'topmanageapi_api_v1_customer_consultList','auth'=>true],
            'customer.consult.reply' => ['uses' => 'topmanageapi_api_v1_customer_consultReply','auth'=>true],
            'customer.consult.display' => ['uses' => 'topmanageapi_api_v1_customer_consultDisplay','auth'=>true],
            'customer.consult.delete' => ['uses' => 'topmanageapi_api_v1_customer_consultDelete','auth'=>true],

            /**
             *  通用工具相关
             **/
            //图片上传
            'image.upload' => ['uses' => 'topmanageapi_api_v1_image_upload','auth'=>true],
        ],
        'v2' => [
            /**
             *  通用类
             **/
            //v2示例1 无需身份验证
            'common.example' => ['uses' => 'topmanageapi_api_v1_example'],
            //v2示例2 需要身份验证
            'common.example2' => ['uses' => 'topmanageapi_api_v1_example', 'auth'=>true],
        ],
    ),
);

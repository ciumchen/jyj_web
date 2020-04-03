<?php

/**
 * ShopEx licence
 * @author Elrond
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class syswechat_finder_wechat{


    public $column_edit = '编辑';
    public $column_edit_order = 2;

    /**
     * 编辑
     * @var array
     * @return html
     */
    public function column_edit(&$colList, $list)
    {
        foreach($list as $k=>$row)
        {
            $url = '?app=syswechat&ctl=admin_wechat&act=edit&finder_id='.$_GET['_finder']['finder_id'].'&id='.$row['id'];
            $target = 'dialog::  {title:\''.app::get('syslogistics')->_('编辑微信公众号信息').'\', width:800, height:600}';
            $title = app::get('syswechat')->_('编辑');

            $colList[$k] = '<a href="' . $url . '" target="' . $target . '">' . $title . '</a>';
        }
    }
}


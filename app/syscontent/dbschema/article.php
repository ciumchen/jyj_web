<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

return array (
    'columns' =>
    array (
        'article_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('syscontent')->_('文章ID'),
            'autoincrement' => true,
            'width' => 50,
            'order'=>1,
        ),
        'title' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syscontent')->_('文章标题'),
            'order'=>2,
        ),
        'article_logo' => array(
            'type' => 'string',
            'label' => app::get('syscontent')->_('文章logo'),
            'order'=>9,
            'comment' => app::get('syscontent')->_('文章默认logo'),
        ),
        'platform' => array(
            'type' => array(
                'pc' => app::get('syscontent')->_('电脑端'),
                'mobile' => app::get('syscontent')->_('移动端'),
                'wap' => app::get('syscontent')->_('移动端'),
            ),
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syscontent')->_('发布终端'),
            'order'=>3,
        ),
        'node_id' =>array (
            'type' => 'number',
            'required' => true,
           /* 'in_list' => true,
            'default_in_list' => true,*/
            'label' => app::get('syscontent')->_('节点id'),
            'order'=>4,
        ),
        'pubtime' => array(
            'type' => 'time',
            'comment' => app::get('syscontent')->_('发布时间（无需精确到秒）'),
            'editable' => true,
            'width' => 130,
            'order'=>6,
        ),
        'modified' =>array (
            'type' => 'time',
            'comment' => app::get('syscontent')->_('更新时间（精确到秒）'),
            'label' => app::get('syscontent')->_('更新时间'),
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>7,
        ),
        'ifpub' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('syscontent')->_('发布'),
            'editable' => true,
            'width' => 40,
            'order'=>8,
        ),
        'content' =>array (
            'type' => 'text',
            'comment'=> app::get('syscontent')->_('文章内容'),
            'editable' => true,
        ),
        'type' =>array (
                'type' => array(
                        '1' => app::get('syscontent')->_('普通文章'),
                        '2' => app::get('syscontent')->_('单独页'),
                ),
                'label' => app::get('syscontent')->_('文章类型'),
                'required' => false,
                'default' => 1,
                'width' => 80,
                'filtertype' => 'yes',
                'filterdefault' => true,
                'in_list' => true,
                'default_in_list' => false,
        ),
        'tmpl_path' =>array (
                'type' => 'string',
                'default' => '',
                'label'=> app::get('syscontent')->_('单独页模板'),
                'editable' => false,
        ),
    ),
    'primary' => 'article_id',
    'index' => array(
        'ind_title' => ['columns' => ['title']],
        'ind_node_id' => ['columns' => ['node_id']],
        'ind_pubtime' => ['columns' => ['pubtime']],
    ),
    'comment' => app::get('syscontent')->_('文章主表'),
);

<?php
return  array(
    'columns'=> array(
        'id' => array(
            'type'=>'number',
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('sysopen')->_('ID'),
        ),
        'shop_name' => array(
            'type' => 'string',
            'comment' => app::get('sysopen')->_('名称'),
            'label' => app::get('sysopen')->_('名称'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'node_type' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('对方节点类型'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'api_ver' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('对方API版本'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'to_node_id' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('对方节点ID'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'bind_status' => array(
            'type' => array(
                'unbind'=> '未绑定',
                'wait' => '待审核',
                'bind' => '已绑定',
                'binded'=> '拒绝绑定',
            ),
            'required' => true,
            'label' => app::get('sysopen')->_('绑定状态'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'is_valid' => array(
            'type' => array(
                '0' => '未开启',
                '1' => '已开启',
            ),
            'default' => '0',
            'required' => true,
            'label' => app::get('sysopen')->_('开启状态'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 20,
        ),
    ),
    'primary' => 'id',
    'comment' => app::get('sysopen')->_('平台Shopex体系内绑定关系表'),
);


<?php
return  array(
    'columns' => array(
        'sku_id' => array(
            'type' => 'table:sku',
            'required' => true,
            'comment' => app::get('sysitem')->_('商品sku_id'),
        ),
        'item_id' => array(
            'type' => 'table:item',
            'required' => true,
            'comment' => app::get('sysitem')->_('商品id'),
        ),
		'grade_id' =>
		array (
		  'type' => 'table:user_grade@sysuser',
		  'required' => true,
		  'default' => 0,
		  'editable' => false,
		  'comment' => app::get('sysitem')->_('会员等级ID'),
		),
		'price' => array(
            'type' => 'decimal',
			'precision' => 5,
            'scale' =>3,
            'required' => true,
            'default' =>'1',
            'in_list' => true,
            'default_in_list' => false,
            'comment' => app::get('sysitem')->_('等级折扣率'),
            'label' => app::get('sysitem')->_('等级折扣率'),
        ),		
    ),
    'primary' => 'item_id',
    'index' => array(
        'ind_item_id' => ['columns' => ['item_id']],
        'ind_sku_id' => ['columns' => ['sku_id']],
    ),
    'comment' => app::get('sysitem')->_('商品等级折扣价格'),
);

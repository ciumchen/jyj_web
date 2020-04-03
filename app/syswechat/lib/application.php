<?php

use EasyWeChat\Foundation\Application as ApplicationA;

class syswechat_application extends ApplicationA
{
    public function __construct($eid = 'platform')
    {
        $options = kernel::single('syswechat_options')->getOptions($eid);
        parent::__construct($options);
    }
}

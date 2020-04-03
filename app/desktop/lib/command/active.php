<?php

class desktop_command_active extends base_shell_prototype
{

    public $command_clean = "清除激活码";
    public function command_clean()
    {
        kernel::single('base_license_active')->cleanActiveCode();
        logger::info('Clean active code');
        return true;
    }

    public $command_info = "查看激活信息";
    public function command_info()
    {
        $args = kernel::single('base_license_active')->getActiveInfo();
        logger::info('Active code' . json_encode($args));
        return true;
    }


}

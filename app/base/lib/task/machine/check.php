<?php

class base_task_machine_check extends base_task_abstract implements base_interface_task
{
    public function exec($params=null)
    {
        if(!is_null($params))
            kernel::single('base_license_machine_check')->check($params);
        return true;
    }
}


<?php

class systag_ctl_admin_tags extends desktop_controller
{
    public function index()
    {
        return $this->finder('systag_mdl_tag',array(
            'title' => app::get('systag')->_('标签列表'),
            'use_buildin_delete' => true,
            'actions'=>array(
                array(
                    'label'=>app::get('syscategory')->_('添加标签'),
                    'href'=>'?app=systag&ctl=admin_tags&act=edit','target'=>'dialog::{title:\''.app::get('syscategory')->_('添加标签').'\',width:500,height:350}'
                ),
            )
        ));
    }

    public function edit()
    {
        $tagId = input::get('tag_id');
        if($tagId > 0)
        {
            $tag = app::get('systag')->model('tag')->getRow('*', ['tag_id'=>$tagId]);
        }
        $pagedata['tag'] = $tag;
        return $this->page('systag/admin/tag/edit.html', $pagedata);
    }

    public function save()
    {
        $this->begin();
        try{
            $tag['tag_id']    = input::get('tag_id', 0);
            $tag['domain']    = input::get('domain');
            $tag['tag_name']  = input::get('tag_name');
            $tag['tag_color'] = input::get('tag_color');

            kernel::single('systag_tag')->saveTag($tag['tag_id'], $tag['domain'], $tag['tag_name'], $tag['tag_color']);
        }catch(Exception $e){
            return $this->end(false, $e->getMessage());
        }
        return $this->end(true, app::get('sysuser')->_('保存成功'));
    }

}


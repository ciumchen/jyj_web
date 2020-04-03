<?php

class systag_api_tag_create{

    public $apiDescription = "创建新的标签";
    public function getParams()
    {
        $return['params'] = array(
            'tag_id'    => ['type'=>'int','valid'=>'', 'default'=>'0', 'example'=>'', 'description'=>'标签id'],
            'domain'    => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'image', 'description'=>'标签作用场景'],
            'tag_name'  => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'标签1', 'description'=>'标签名称'],
            'tag_color' => ['type'=>'string','valid'=>'', 'default'=>'#cccccc', 'example'=>'#cccccc', 'description'=>'标签颜色'],
        );
        return $return;
    }

    public function save($params)
    {
        $tag_id = $params['tag_id']? : 0;
        $domain = $params['domain'];
        $tag_name = $params['tag_name'];
        $tag_color = $params['tag_color'] ?  : '#cccccc';

        $tagId = kernel::single('systag_tag')->saveTagWithTrans($tag_id, $domain, $tag_name, $tag_color);
        return $tagId;
    }
}

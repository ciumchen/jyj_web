<?php

class systag_api_rels_getTagsByOwner
{

    public $apiDescription = "获取某个对象关联的所有id";

    public function getParams()
    {
        $return['params'] = array(
            'domain'    => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'image', 'description'=>'标签作用场景'],
            'rel_ids'   => ['type'=>'array','valid'=>'required', 'default'=>'', 'example'=>'image', 'description'=>'标签关联对象的id的集合'],
        );
        return $return;
    }

    public function get($params)
    {
        $domain  = $params['domain'];
        $rel_ids = $params['rel_ids'];

        return kernel::single('systag_rels')->readTags($domain, $rel_ids);

    }

}



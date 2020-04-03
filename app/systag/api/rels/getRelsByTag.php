<?php

class systag_api_rels_getRelsByTag
{

    public $apiDescription = "获取某个对象关联的所有id";

    public function getParams()
    {
        $return['params'] = array(
            'tag_id'    => ['type'=>'array','valid'=>'', 'default'=>'0', 'example'=>'', 'description'=>'标签id的集合'],
            'domain'    => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'image', 'description'=>'标签作用场景'],
            'offset'    => ['type'=>'int','valid'=>'int','description'=>'前面有多少条数据不显示','example'=>'','default'=>'0'],
            'limit'     =>['type'=>'int','valid'=>'int','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
        );
        return $return;
    }

    public function get($params)
    {
        $domain  = $params['domain'];
        $tag_id  = $params['tag_id'];
        $offset  = $params['offset'] ? :0;
        $limit   = $params['limit'] ? :100;

        return kernel::single('systag_rels')->readRels($domain, $tag_id, $offset, $limit);

    }

}



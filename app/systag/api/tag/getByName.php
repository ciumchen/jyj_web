<?php
class systag_api_tag_getByName{

    public $apiDescription = "通过名称查看标签";
    public function getParams()
    {
        $return['params'] = array(
            'domain'    => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'image', 'description'=>'标签作用场景'],
            'tag_name'  => ['type'=>'string','valid'=>'required', 'default'=>'', 'example'=>'标签1', 'description'=>'标签名称'],
        );
        return $return;
    }

    public function get($params)
    {
        $domain = $params['domain'];
        $tag_name = $params['tag_name'];

        $tag = kernel::single('systag_tag')->getByTagName($domain, $tag_name);
        return $tag;
    }
}

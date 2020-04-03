<?php

class systag_api_tag_delete{

    public $apiDescription = "删除标签";
    public function getParams()
    {
        $return['params'] = array(
            'tag_id'    => ['type'=>'int','valid'=>'required', 'default'=>'0', 'example'=>'', 'description'=>'标签id'],
        );
        return $return;
    }

    public function delete($params)
    {
        $tag_id = $params['tag_id'];
        $tagId = kernel::single('systag_tag')->delete($tag_id);
        return $tagId;
    }
}

<?php
class systag_api_tag_list{

    public $apiDescription = "查看标签";
    public function getParams()
    {
        $return['params'] = array(
            'domain'    => ['type'=>'string','valid'=>'', 'default'=>'', 'example'=>'image', 'description'=>'标签作用场景'],
            'page_no' => ['type'=>'int','valid'=>'int','description'=>'分页当前页码,1<=no<=499','example'=>'','default'=>'1'],
            'page_size' =>['type'=>'int','valid'=>'int','description'=>'分页每页条数(1<=size<=200)','example'=>'','default'=>'40'],
        );
        return $return;
    }

    public function get($params)
    {
        $domain = $params['domain'] ? : null;
        $pagesize = $params['page_size'] ? : 40;
        $pageno   = $params['page_no'] ? : 1;

        $tags = kernel::single('systag_tag')->getList($domain, $pageno, $pagesize);
        return $tags;
    }
}

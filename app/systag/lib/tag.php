<?php
class systag_tag
{
    private $tag_limit = 100;

    public function saveTagWithTrans($tag_id, $domain, $tag_name, $tag_color)
    {

        $db = app::get('systag')->database();
        $db->beginTransaction();
        try{
            $ret = $this->saveTag($tag_id, $domain, $tag_name, $tag_color);
            $db->commit();
        }catch(Exception $e){
            $db->rollback();
            throw $e;
        }

        return $ret;
    }

    /**
     * @brief 获取标签列表
     * @param string domain
     * @param int pageno
     * @param int pagesize
     * @return array list 列表
     * @return int count 总计数量
     *
     */
    public function getList($domain = null, $pageno = 1, $pagesize = 10)
    {
        $filter = [];
        if($domain) $filter['domain'] = $domain;

        $limit = $pagesize;
        $offset = ($pageno-1) * $limit;

        $tagMdl = app::get('systag')->model('tag');
        $list   = $tagMdl->getList('*', $filter, $offset, $limit);
        $count  = $tagMdl->count($filter);
        return ['list'=>$list, 'count'=>$count];
    }

    public function getByTagName($domain, $tag_name)
    {
        $filter = [
            'domain' => $domain,
            'tag_name' => $tag_name,
        ];

        $tagMdl = app::get('systag')->model('tag');
        $tag    = $tagMdl->getRow('*', $filter);
        return $tag;
    }

    public function saveTag($tag_id, $domain, $tag_name, $tag_color)
    {
        $this->checkSaveTag($tag_id, $domain, $tag_name, $tag_color);
        $tag = [
            'domain' => $domain,
            'tag_name' => $tag_name,
            'tag_color' => $tag_color,
        ];
        if($tag_id > 0) $tag['tag_id'] = $tag_id;

        app::get('systag')->model('tag')->save($tag);
        $tid = app::get('systag')->model('tag')->getRow('tag_id', ['tag_name'=>$tag_name, 'domain'=>$domain]);

        return $tid['tag_id'];
    }

    public function delete($tag_id)
    {
        if($tag_id > 0) app::get('systag')->model('tag')->delete(['tag_id'=>$tag_id]);
        else throw new LogicException(app::get('systag')->_('标签id只能是正整数'));
        return true;
    }

    private function checkSaveTag($tag_id, $domain, $tag_name, $tag_color)
    {
        //为了防止滥用标签，所以用了这个东西限制一下
        $count = app::get('systag')->model('tag')->count(['domain'=>$domain]);
        if($count > $this->tag_limit) throw new LogicException('标签数量太多了，请删除无用的标签');

        //优化重复添加的报错信息
        $count = app::get('systag')->model('tag')->count(['tag_name'=>$tag_name, 'domain'=>$domain]);
        if($count > 0) throw new LogicException('标签已经存在，请不要重复添加');
    }

}


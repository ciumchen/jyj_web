<?php
class systag_rels
{
    private $scene = 'systag';

    private $engine = null;

    public function __construct()
    {
        $this->engine = kernel::single('systag_rels_redis');
    }

    public function binds($domain, $tag_ids, $rel_ids, $isShell = false)
    {

        if(!is_array($tag_ids)) $tag_ids = [$tag_ids];
        if(!is_array($rel_ids)) $rel_ids = [$rel_ids];

        //为了防止web超时，这里做了一个点
        if(!$isShell){
            if(count($tag_ids) > 100)
                throw new LogicException('一次放入太多的标签了，如需导入大量数据，请在命令行下执行');
            if(count($rel_ids) > 100)
                throw new LogicException('一次选择太多的数据了，如需导入大量数据，请在命令行下执行');
        }

        foreach($tag_ids as $tag_id)
        {
            foreach($rel_ids as $rel_id)
            {
                $this->bind($domain, $tag_id, $rel_id);
            }
        }
        return true;
    }

    public function unbinds($domain, $tag_ids, $rel_ids, $isShell = false)
    {
        if(!is_array($tag_ids)) $tag_ids = [$tag_ids];
        if(!is_array($rel_ids)) $rel_ids = [$rel_ids];

        //为了防止web超时，这里做了一个点
        if(!$isShell){
            if(count($tag_ids) > 100)
                throw new LogicException('一次放入太多的标签了，如需导入大量数据，请在命令行下执行');
            if(count($rel_ids) > 100)
                throw new LogicException('一次选择太多的数据了，如需导入大量数据，请在命令行下执行');
        }

        foreach($tag_ids as $tag_id)
        {
            foreach($rel_ids as $rel_id)
            {
                $this->unbind($domain, $tag_id, $rel_id);
            }
        }
        return true;
    }

    public function bind($domain, $tag_id, $rel_id)
    {
        logger::info('systag bind tag:' . json_encode(['domain'=>$domain,'tagId'=>$tag_id, 'relId'=>$rel_id]));
        $this->engine->bind($domain, $tag_id, $rel_id);
        return true;
    }

    public function unbind($domain, $tag_id, $rel_id)
    {
        logger::info('systag unbind tag:' . json_encode(['domain'=>$domain,'tagId'=>$tag_id, 'relId'=>$rel_id]));
        $this->engine->unbind($domain, $tag_id, $rel_id);
        return true;
    }

    public function bindCount($domain, $tag_id)
    {
        return $this->engine->bindCount($domain, $tag_id);
    }

    public function readTags($domain, $rel_ids)
    {
        if(!is_array($rel_ids)) $rel_ids = [$rel_ids];
        //获取所有的相关tag
        $allTags = kernel::single('systag_tag')->getList($domain, 1, 1000);
        $formatAllTags = [];
        foreach($allTags['list'] as $tag)
        {
            $tag_id = $tag['tag_id'];
            $tag['rel_count'] = $this->bindCount($domain, $tag_id);
            $formatAllTags[$tag_id] = $tag;
        }

        //组织tags数据
        $formatTags = [];
        foreach($rel_ids as $rel_id)
        {
            $tag_ids = $this->engine->readTags($domain, $rel_id);
            foreach($tag_ids as $tag_id)
            {
                $formatTags[$rel_id][$tag_id] = $formatAllTags[$tag_id];
            }
        }
        return $formatTags;
    }

    public function readRels($domain, $tag_id, $offset = 0, $limit = 40)
    {
        return $this->engine->readRels($domain, $tag_id, $offset, $limit);
    }

    //这个方法不要调用，会清楚所有关联关系！
    //这个方法不要调用，会清楚所有关联关系！
    //这个方法不要调用，会清楚所有关联关系！
    //重要的事情说三遍
    public function cleanAllRels()
    {
        $keys = redis::scene('systag')->keys('systag:*');
        foreach($keys as $key)
        {
            $key = substr($key, 7, strlen($key) - 6);
            redis::scene($this->scene)->del($key);
        }

        return true;
    }

}


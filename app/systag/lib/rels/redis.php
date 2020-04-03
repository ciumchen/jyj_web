<?php

class systag_rels_redis
{

    private $__scene = 'systag';

    public function bind($domain, $tag_id, $rel_id)
    {
        $this->__zadd($domain, $tag_id, $rel_id);
        $this->__sadd($domain, $tag_id, $rel_id);
        return true;
    }

    public function unbind($domain, $tag_id, $rel_id)
    {
        $this->__zrm($domain, $tag_id, $rel_id);
        $this->__srm($domain, $tag_id, $rel_id);
        return true;
    }

    public function bindCount($domain, $tag_id)
    {
        return $this->__zcard($domain, $tag_id);
    }

    public function readTags($domain, $rel_id)
    {
        return $this->__smembers($domain, $rel_id);
    }

    public function readRels($domain, $tag_id, $offset, $limit)
    {
        $min = $offset;
        $max = $offset + $limit;

        return $this->__zrevrange($domain, $tag_id, $min, $max);

    }

    private function __genRelsKey($domain, $tag_id, $rel_id)
    {
        $key = "systag:tagRel:$domain:tag" . $tag_id;
        return $key;
    }

    private function __genOwnKey($domain, $tag_id, $rel_id)
    {
        $key = "systag:tagRel:$domain:item" . $rel_id;
        return $key;
    }

    private function __zadd($domain, $tag_id, $rel_id)
    {

        redis::scene($this->__scene)->zadd(
            $this->__genRelsKey($domain, $tag_id, $rel_id),
            $rel_id,
            $rel_id
        );
        return true;
    }

    private function __zrm($domain, $tag_id, $rel_id)
    {
        redis::scene($this->__scene)->zrem(
            $this->__genRelsKey($domain, $tag_id, $rel_id),
            $rel_id
        );
        return true;
    }

    private function __zcard($domain, $tag_id)
    {

        $tag_ids = redis::scene($this->__scene)->zcard(
            $this->__genRelsKey($domain, $tag_id, 0)
        );
        return $tag_ids;
    }

    private function __zrevrange($domain, $tag_id, $min, $max)
    {
        $rel_ids = redis::scene($this->__scene)->zrevrange(
            $this->__genRelsKey($domain, $tag_id, 0),
            $min, $max
        );
        return $rel_ids;
    }

    private function __sadd($domain, $tag_id, $rel_id)
    {
        redis::scene($this->__scene)->sadd(
            $this->__genOwnKey($domain, $tag_id, $rel_id),
            $tag_id
        );
        return true;
    }

    private function __srm($domain, $tag_id, $rel_id)
    {
        redis::scene($this->__scene)->srem(
            $this->__genOwnKey($domain, $tag_id, $rel_id),
            $tag_id
        );
        return true;
    }

    private function __smembers($domain, $rel_id)
    {
        $tag_ids = redis::scene($this->__scene)->smembers(
            $this->__genOwnKey($domain, 0, $rel_id)
        );
        return $tag_ids;
    }

    private function __scard($domain, $rel_id)
    {

        $tag_ids = redis::scene($this->__scene)->scard(
            $this->__genOwnKey($domain, 0, $rel_id)
        );
        return $tag_ids;
    }

}

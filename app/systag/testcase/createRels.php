<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class createRels extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->domain = 'image';
        $this->tag_ids = [1,2,3,4,5];
        $this->rel_ids = [6,7,8,9,10];
    }


    public function testBind()
    {
        $ret = kernel::single('systag_rels')->binds(
            $this->domain,
            $this->tag_ids,
            $this->rel_ids);
        logger::info('bind tags successful with params:' . json_encode(
            ['domain'=>$this->domain, 'tag_ids' => $this->tag_ids, 'rel_ids'=>$this->rel_ids]
        ));
    }

    public function testReadRels()
    {

    }

    public function testReadTags()
    {

        $ret = kernel::single('systag_rels')->readTags(
            $this->domain,
            $this->rel_ids
        );
        logger::info(
            'read tags by rel_ids:' . var_export(
                [
                    'domain' => $this->domain,
                    'rel_ids' => $this->rel_ids,
                    'return' => $ret
                ]
            )
        );

    }

    public function testReadRelsId()
    {
        $formatRet = [];
        foreach($this->tag_ids as $tag_id)
        {
            $ret = kernel::single('systag_rels')->readRels($this->domain, $tag_id);
            $formatRet[$tag_id]  = $ret;
        }
        logger::info(
            'read tags by rel_ids:' . var_export(
                [
                    'domain' => $this->domain,
                    'tag_ids' => $this->tag_ids,
                    'return' => $formatRet,
                ]
            )
        );
    }

    public function testUnbind()
    {
        $ret = kernel::single('systag_rels')->unbinds(
            $this->domain,
            $this->tag_ids,
            $this->rel_ids);
        logger::info('unbind tags successful as params:' . json_encode(
            ['domain'=>$this->domain, 'tag_ids' => $this->tag_ids, 'rel_ids'=>$this->rel_ids]
        ));

    }

}


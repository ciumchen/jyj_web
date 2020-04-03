<?php
/**
 * topapi
 *
 * -- article.save
 * -- 编辑文章
 *
 * @copyright  Copyright (c) 2005-2016 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class topmanageapi_api_v1_article_save implements topmanageapi_interface_api{

    /**
     * 接口作用说明
     */
    public $apiDescription = '添加文章';

    /**
     * 定义API传入的应用级参数
     * @desc 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入，并且定义参数的数据类型，参数是否必填，参数的描述
     * @return array 返回传入参数
     */
    public function setParams()
    {
        return [
            'id'              => ['type'=>'int', 'valid'=>'','desc'=>'文章id','msg'=>'文章id'],
            'title'             => ['type'=>'string', 'valid'=>'required|min:2|max:50','desc'=>'文章标题','msg'=>'文章标题不能为空|文章标题至少两个字|文章标题最多五十字'],
            'use_platform'      => ['type'=>'int', 'valid'=>'required','desc'=>'0: 电脑端和手机端,1: 电脑端,2: 手机端'],
            'node'              => ['type'=>'int', 'valid'=>'required','desc'=>'分类id','msg'=>'请选择文章分类'],
            'pubtime'           => ['type'=>'string', 'valid'=>'required','desc'=>'发布时间例子:2017-09-20 15:47:35','msg'=>'发布时间不能为空'],
            'content'           => ['type'=>'string', 'valid'=>'required|min:8', 'desc'=>'文章内容','msg'=>'文章内容不能为空|文章内容至少八个字']
        ];
    }
 
    public function handle($params)
    {
        //判断发布时间误差为一分钟,编辑文章不需要判断
        if(!$params['id'])
        {
            $this->__checkPubtime($params['pubtime']);
        }
        try {
            $postData['shop_id'] = $params['oAuthInfo']['shop_id'];
            $postData['title']   = htmlspecialchars($params['title']);
            $postData['content']   = $params['content'];
            $postData['article_id']   = $params['id'] ? $params['id'] : '';
            $postData['platform']   = $params['use_platform'];
            $postData['node_id']   = trim($params['node']);
            $postData['pubtime'] = strtotime($params['pubtime']);
            $postData['modified'] = time();
            $result = app::get('topshop')->rpcCall('syscontent.shop.save.article', $postData);
            if(!$result)
            {
                return ['status'=>'error','msg'=>'保存失败'];
            }
        } catch (Exception $e) {
            $msg = $e->getMessage ();
            throw new \LogicException(app::get('topmanageapi')->_($msg));
        }
        return ['status'=>'success','msg'=>'发布成功'];
    }

/**
 *  校验发布时间
 * @AuthorHTL
 * @DateTime  2017-09-20T16:12:27+0800
 * @param     [string]                   $pubtime [发布时间]
 * @return    [array]                            [错误信息]
 */
    private function __checkPubtime($pubtime)
    {
        $pubtime = strtotime($pubtime)+60;
        if ($pubtime<time())
        {
            return ['status'=>'error','msg'=>'发布时间不能小于当前时间'];
        }
    }
}


<?php
class syssupport_notice
{

    private $product = 'bbc';

    private $url = '';

    private $defaultUrl = 'https://ego.shopex.cn/api/shopex/ad';

    private $htmlUrl = '';

    private $defaultHtmlUrl = 'https://ego.shopex.cn/api/shopex/ad.html';

    public function __construct()
    {
        $this->url = config::get('link.shopex_ego_notice', $this->defualtUrl);
        $this->htmlUrl = config::get('link.shopex_ego_notice_html', $this->defaultHtmlUrl);
    }

    //获取要提交到的接口网址
    public function getUrl()
    {
        return $this->url;
    }

    //获取提交的表单内容
    public function getParams()
    {
        $params = [
            'product' => $this->getProduct(),
            'code' => $this->getCode(),
        ];

        return $params;
    }

    //获取auth_code，这个code是用来登录的
    public function getCode()
    {
        return kernel::single('syssupport_auth_code')->getCode();
    }

    //获取产品类型，在ego平台上面的bbc,决定签名的
    public function getProduct()
    {
        return $this->product;
    }

    public function checkParams($params)
    {
        if(!$params['certificate_id']) return false;
        if(!$params['shopex_id']) return false;
        if(!$params['active_key']) return false;
        if(!$params['version']) return false;
        if(!$params['product_name']) return false;

        return true;
    }

    public function getNotice()
    {
        $url = $this->getUrl();
        $params = $this->getParams();
 //     var_dump(['url'=>$url, 'params'=>$params]);exit;
        $res = client::post($url, ['form_params'=>$params])->getBody();
        $res = json_decode($res, 1);

        if($res['code'] === 0)
            return $res;
        return false;
    }

    public function getNoticeIframeUrl()
    {
        return $this->defaultHtmlUrl . '?' . http_build_query($this->getParams());
    }
}


<?php
class syssupport_auth_code
{

    private $deploy = [];

    public function __construct()
    {
        $this->deploy = $this->getDeploy();
    }

    public function getCode()
    {
        return $this->encode($this->getParams());
    }

    public function getParams()
    {
        $params = [
            'node_id' => $this->getNodeId(),
            'certificate_id' => $this->getCertificateId(),
            'shopex_id' => $this->getShopexId(),
            'active_key' => $this->getActiveKey(),
            'url' => $this->getUrl(),
            'version' => $this->getVersion(),
            'product_name' => $this->getProductName(),
            'custom_dir' => $this->getCustomDir(),
        ];
        return $params;
    }

    public function encode($params, $expire)
    {
        if($expire === 0)
            kernel::single('base_license_sign')->setExpire(300);
        elseif($expire > 0)
            kernel::single('base_license_sign')->setExpire($expire);
        return kernel::single('base_license_sign')->encode($params);
    }

    public function getNodeId()
    {
        $requestParams = ['shop_id'=>0];
        $nodeInfo = app::get('syssupport')->rpcCall('open.shop.node.get', $requestParams);
        return $nodeInfo['node_id'] ? $nodeInfo['node_id'] : 0;
    }

    public function getCertificateId()
    {
        return base_certi::certi_id();
    }

    public function getShopexId()
    {
        return base_enterprise::ent_id();
    }

    public function getActiveKey()
    {
        return app::get('desktop')->getConf('activation_code');
    }

    public function getUrl()
    {
        return url::action('topc_ctl_default@index');
    }

    public function getVersion()
    {
        return $this->deploy['product_version'];
    }

    public function getProductName()
    {
        $zl = ecos_get_code_license_info();
        return $zl['Product-Name'];
    }

    public function getCustomDir()
    {
        if(defined('CUSTOM_CORE_DIR')) $dir = CUSTOM_CORE_DIR;
        else $dir = '';
        return $dir;
    }

    public function getDeploy()
    {
        return kernel::single('base_xml')->xml2array(file_get_contents(ROOT_DIR.'/config/deploy.xml'),'base_deploy');
    }
}

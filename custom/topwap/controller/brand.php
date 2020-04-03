<?php
class topwap_ctl_brand extends topwap_controller{
    public function index()
    {
        $this->setLayoutFlag('brand');
        return $this->page('topwap/brand/index.html',[]);
    }

}

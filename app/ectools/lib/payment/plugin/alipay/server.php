<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/**
 * alipay notify 验证接口
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.payment.plugin
 */
class ectools_payment_plugin_alipay_server extends ectools_payment_app {

	/**
	 * 支付后返回后处理的事件的动作
	 * @params array - 所有返回的参数，包括POST和GET
	 * @return null
	 */
    public function callback(&$recv)
	{
        #键名与pay_setting中设置的一致
        $mer_id = $this->getConf('mer_id', substr(__CLASS__, 0, strrpos(__CLASS__, '_')));
        $mer_id = $mer_id == '' ? '2088002003028751' : $mer_id;
        $mer_key = $this->getConf('mer_key', substr(__CLASS__, 0, strrpos(__CLASS__, '_')));
        $mer_key = $mer_key=='' ? 'afsvq2mqwc7j0i69uzvukqexrzd0jq6h' : $mer_key;

        if($this->is_return_vaild($recv,$mer_key)){
            $ret['payment_id'] = $recv['out_trade_no'];
			$ret['account'] = $mer_id;
			$ret['bank'] = app::get('ectools')->_('支付宝');
			$ret['pay_account'] = app::get('ectools')->_('付款帐号');
			$ret['currency'] = 'CNY';
			$ret['money'] = $recv['total_fee'];
			$ret['paycost'] = '0.000';
			$ret['cur_money'] = $recv['total_fee'];
            $ret['trade_no'] = $recv['trade_no'];
			$ret['t_payed'] = strtotime($recv['notify_time']) ? strtotime($recv['notify_time']) : time();
			$ret['pay_app_id'] = "alipay";
			$ret['pay_type'] = 'online';
			$ret['memo'] = $recv['body'];

            switch($recv['trade_status']){
				// ipn方式回来
				case 'WAIT_BUYER_PAY':
					echo "success";
					$ret['status'] = 'ready';
					break;
                case 'TRADE_FINISHED':
					echo "success";
                    $ret['status'] = 'succ';
                    break;
                case 'TRADE_SUCCESS':
					echo "success";
                    $ret['status'] = 'succ';
                    break;
           }

        }else{
            $ret['message'] = 'Invalid Sign';
            $ret['status'] = 'invalid';
        }

		return $ret;
    }

    /**
     * 检验返回数据合法性
     * @param mixed $form 包含签名数据的数组
     * @param mixed $key 签名用到的私钥
     * @access private
     * @return boolean
     */
    public function is_return_vaild($form,$key)
	{
        ksort($form);
        foreach($form as $k=>$v){
            if($k!='sign'&&$k!='sign_type'){
                $signstr .= "&$k=$v";
            }
        }

        $signstr = ltrim($signstr,"&");
        $signstr = $signstr.$key;

        if($form['sign']==md5($signstr)){
            return true;
        }
        #记录返回失败的情况
        logger::error(app::get('ectools')->_('支付单号：') . $form['out_trade_no'] . app::get('ectools')->_('签名验证不通过，请确认！')."\n");
        logger::error(app::get('ectools')->_('本地产生的加密串：') . $signstr);
        logger::error(app::get('ectools')->_('支付宝传递打过来的签名串：') . $form['sign']);
		$str_xml .= "<alipayform>";
        foreach ($form as $key=>$value)
        {
            $str_xml .= "<$key>" . $value . "</$key>";
        }
        $str_xml .= "</alipayform>";

        return false;
    }

    public function generateSign($params, $signType = "RSA",$priKey) {
        return $this->sign($this->getSignContent($params), $signType,$priKey);
    }

    public function getSignContent($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    protected function sign($data, $signType = "RSA",$priKey) {
            if (empty($priKey)) {
                throw new Exception('私钥不存在，请检查支付方式配置');
            }
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                    wordwrap($priKey, 64, "\n", true) .
                 "\n-----END RSA PRIVATE KEY-----";

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        if(!$this->checkEmpty($this->rsaPrivateKeyFilePath)){
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {

        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //              $data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }


        return $data;
    }
}

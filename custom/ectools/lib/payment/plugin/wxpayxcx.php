<?php
/**
 * Created by PhpStorm.
 * User: yangminsheng
 * Date: 2018/8/20
 * Time: 下午6:00
 */
/**
 * 微信小程序支付接口
 * @auther shopex ecstore dev dev@shopex.cn
 * @version 0.1
 * @package ectools.lib.payment.plugin
 */
final class ectools_payment_plugin_wxpayxcx extends ectools_payment_app implements ectools_interface_payment_app
{


    /**
     * @var string 支付方式名称
     */
    public $name = '微信支付小程序接口';
    /**
     * @var string 支付方式接口名称
     */
    public $app_name = '微信支付小程序接口';
    /**
     * @var string 支付方式key
     */
    public $app_key = 'wxpayxcx';
    /**
     * @var string 中心化统一的key
     */
    public $app_rpc_key = 'wxpayxcx';
    /**
     * @var string 统一显示的名称
     */
    public $display_name = '微信支付小程序接口';
    /**
     * @var string 货币名称
     */
    public $curname = 'CNY';
    /**
     * @var string 当前支付方式的版本号
     */
    public $ver = '1.0';
    /**
     * @var string 当前支付方式所支持的平台
     */
    public $platform = 'iswxxcx';

    /**
     * @var array 扩展参数
     */
    public $supportCurrency = array("CNY"=>"CNY");

    /**
     * @微信支付固定参数
     */
    public $init_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder?';

    /**
     * 构造方法
     * @param null
     * @return boolean
     */
    public function __construct($app){
        parent::__construct($app);

        $this->notify_url = url::to('wap/wxpayxcx.html');
        $this->unifiedorder_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $this->submit_charset = 'UTF-8';

    }


    /**
     * 后台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function admin_intro(){
        $regIp = isset($_SERVER['SERVER_ADDR'])?$_SERVER['SERVER_ADDR']:$_SERVER['HTTP_HOST'];
        return '<img src="' . app::get('weixin')->res_url . '/payments/images/WXPAY.jpg"><br /><b style="font-family:verdana;font-size:13px;padding:3px;color:#000"><br>微信支付(JSAPI V3.3.6)是由腾讯公司知名移动社交通讯软件微信及第三方支付平台财付通联合推出的移动支付创新产品，旨在为广大微信用户及商户提供更优质的支付服务，微信的支付和安全系统由腾讯财付通提供支持。</b>
            <br>如果遇到支付问题，请访问：<a href="javascript:void(0)" onclick="top.location = '."'http://bbs.ec-os.net/read.php?tid=1007'".'">http://bbs.ec-os.net/read.php?tid=1007</a>';
    }

    /**
     * 后台配置参数设置
     * @param null
     * @return array 配置参数列表
     */
    public function setting(){

        return array(
            'pay_name'=>array(
                'title'=>app::get('ectools')->_('支付方式名称'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'appId'=>array(
                'title'=>app::get('ectools')->_('appId'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'Mchid'=>array(
                'title'=>app::get('ectools')->_('Mchid'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'Key'=>array(
                'title'=>app::get('ectools')->_('Key'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'Appsecret'=>array(
                'title'=>app::get('ectools')->_('Appsecret'),
                'type'=>'string',
                'validate_type' => 'required',
            ),
            'order_by' =>array(
                'title'=>app::get('ectools')->_('排序'),
                'type'=>'string',
                'label'=>app::get('ectools')->_('整数值越小,显示越靠前,默认值为1'),
            ),
            'support_cur'=>array(
                'title'=>app::get('weixin')->_('支持币种'),
                'type'=>'text hidden cur',
                'options'=>$this->arrayCurrencyOptions,
            ),
            'pay_fee'=>array(
                'title'=>app::get('ectools')->_('交易费率'),
                'type'=>'pecentage',
                'validate_type' => 'number',
            ),
            'pay_desc'=>array(
                'title'=>app::get('ectools')->_('描述'),
                'type'=>'html',
                'includeBase' => true,
            ),
            'pay_type'=>array(
                'title'=>app::get('ectools')->_('支付类型(是否在线支付)'),
                'type'=>'radio',
                'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
                'name' => 'pay_type',
            ),
            'status'=>array(
                'title'=>app::get('ectools')->_('是否开启此支付方式'),
                'type'=>'radio',
                'options'=>array('false'=>app::get('ectools')->_('否'),'true'=>app::get('ectools')->_('是')),
                'name' => 'status',
            ),
            'def_payment'=>array(
                'title'=>app::get('ectools')->_('设为默认支付方式'),
                'type' =>'radio',
                'options'=>array('0'=>app::get('ectools')->_('否'),'1'=>app::get('ectools')->_('是')),
                'name' =>'def_payment',
            ),
        );
    }



    /**
     * 前台支付方式列表关于此支付方式的简介
     * @param null
     * @return string 简介内容
     */
    public function intro(){
        return app::get('ectools')->_('微信支付是由腾讯公司知名移动社交通讯软件微信及第三方支付平台财付通联合推出的移动支付创新产品，旨在为广大微信用户及商户提供更优质的支付服务，微信的支付和安全系统由腾讯财付通提供支持。财付通是持有互联网支付牌照并具备完备的安全体系的第三方支付平台。');
    }

    /**
     * 提交支付信息的接口
     * @param array 提交信息的数组
     * @return mixed false or null
     */
    public function dopay($payment)
    {
        $appid  = trim($this->getConf('appId', __CLASS__));
        $mch_id = trim($this->getConf('Mchid', __CLASS__));
        $key    = trim($this->getConf('Key',   __CLASS__));

        //获取详细内容
        $subject = (isset($payment['subject']) && $payment['subject']) ? $payment['subject'] : ($payment['account'].$payment['payment_id']);
        $subject = str_replace("'",'`',trim($subject));
        $subject = str_replace('"','`',$subject);
        $subject = str_replace(' ','',$subject);
		kernel::single('base_session')->start();
				        logger::info('_SESSION:'.var_export($_SESSION, 1));
		$open_id = $_SESSION['xcxopenid']?$_SESSION['xcxopenid']:$_COOKIE['xcxopenid'];
						file_put_contents(DATA_DIR.'/text.txt',date('y-m-d H:i:s').var_export($_SESSION,1).'p_SESSION',FILE_APPEND);
				file_put_contents(DATA_DIR.'/text.txt',date('y-m-d H:i:s').var_export($_COOKIE,1).'p_COOKIE',FILE_APPEND);
        $parameters = array(
            'appid'            => strval($appid),
            'mch_id'           => strval($mch_id),		
            'nonce_str'        => ectools_payment_plugin_wxpay_util::create_noncestr(),		
            'body'             => strval($payment['payment_id']),			
            'out_trade_no'     => strval($payment['payment_id']),			
            'total_fee'        => bcmul($payment['cur_money'], 100, 0),
            'spbill_create_ip' => strval( $_SERVER['SERVER_ADDR'] ),
            'notify_url'       => strval($this->notify_url),			
            'trade_type'       => 'JSAPI',			
            'sign_type'       => 'MD5',			
            'openid'           => strval($open_id),//input::get('openid')),
        );
		        logger::info('wxpayxcx: post to weixin for parameters:'.var_export($parameters, 1));
		        logger::info('wxpayxcx: post to weixin for key:'.var_export($key, 1));
   $parameters['sign'] = $this->getSign($parameters,$key);
        $xml                = $this->arrayToXml($parameters);
        logger::info('wxpayxcx: post to weixin for parameters:'.var_export($parameters, 1));
        logger::info('wxpayxcx: post to weixin for prepay_id:'.var_export($xml, 1));
		$url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
       // $response           = $this->wxpostXmlCurl($xml, $this->unifiedorder_url, 30);// 获取guzzle返回的值的body部分
		//$response =	$this->postXmlCurl($xml, $url, 30);
		$response = client::post($url, ['body' => $xml])->getBody();// 获取guzzle返回的值的body部分
        logger::info('wxpayxcx response info from weixin for prepay_id:'.var_export((string)$response, 1));
        $result             = $this->xmlToArray($response);
        $prepay_id          = $result['prepay_id'];
		logger::info(var_export($result,1).'result');
        if($prepay_id == '')
        {
            echo $this->get_error_html($result['return_msg']);
        }

        $data =array(
            'appId'     => $appid,
            'timeStamp' => strval(time()),
            'nonceStr'  => ectools_payment_plugin_wxpay_util::create_noncestr(),
            'package'   => 'prepay_id='.$prepay_id,
            'signType'  => 'MD5',
        );
        $data['paySign'] = $this->getSign($data, $key);

        $data['order_id'] = $payment['order_id'];

        echo $this->get_html3($payment['payment_id'], $data, $prepay_id);exit;
    }
    /**
     * 支付后返回后处理的事件的动作
     * @params array - 所有返回的参数，包括POST和GET
     * @return null
     */
    function callback(&$in)
    {
        $mch_id = trim($this->getConf('Mchid', __CLASS__));
        $key    = trim($this->getConf('Key',   __CLASS__));
        $in = $in['weixin_postdata'];
        if( $in['return_code'] == 'SUCCESS' ) 
        {
            if( $in['sign'] == $this->getSign($in, $key))
            {
                $money = ecmath::number_multiple(array($in['total_fee'], 0.01));
                $ret['payment_id' ] = $in['out_trade_no'];
                $ret['account']     = $mch_id;
                $ret['bank']        = app::get('ectools')->_('微信支付小程序');
                $ret['pay_account'] = $in['openid'];
                $ret['currency']    = 'CNY';
                $ret['money']       = $money;
                $ret['paycost']     = '0.00';
                $ret['cur_money']   = $money;
                $ret['trade_no']    = $in['transaction_id'];
                $ret['t_payed']     = strtotime($in['time_end']) ? strtotime($in['time_end']) : time();
                $ret['pay_app_id']  = "wxpayxcx";
                $ret['pay_type']    = 'online';
                $ret['memo']        = $in['attach'];
                $ret['status']      = 'succ';

            }
            else
            {
                $ret['status'] = 'failed';
            }
        }
        else
        {
            $ret['status'] = 'failed';
        }
        return $ret;
    }	


    /**
     * 支付成功回打支付成功信息给支付网关
     */
    function ret_result($paymentId)
    {
        $ret = array('return_code'=>'SUCCESS', 'return_msg'=>'');
        $ret = $this->arrayToXml($ret);
        echo $ret;exit;
    }

    /**
     * 校验方法
     * @param null
     * @return boolean
     */
    public function is_fields_valiad()
    {
        return true;
    }

    /**
     * 生成支付表单 - 自动提交
     * @params null
     * @return null
     */
    public function gen_form()
    {
        return '';
    }

    protected function get_error_html($info)
    {
        if($info == '')
        {
            $info = '系统繁忙，请选择其它支付方式或联系客服。';
        }
        header("Content-Type: text/html;charset=".$this->submit_charset);
        $html = '
            <html>
                <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                <title>微信安全支付</title>
                <script language="javascript">
                    alert("'.$info.'");
                </script>
            </html>
            ';

        return $html;
    }


    protected function get_html3($payment_id, $data, $p){
        header("Content-Type: text/html;charset=".$this->submit_charset);

		
        $appId = $data['appId'];
        $nonceStr = $data['nonceStr'];
        $paySign = $data['paySign'];
       // $callback_url = app::get('wap')->router()->gen_url(array('app'=>'b2c','ctl'=>'wap_paycenter','act'=>'wx_xcx_result','full'=>1,'arg0'=>$data['order_id']));
        $callback_url = url::action('topwap_ctl_paycenter@finish', ['payment_id'=>$payment_id]);  
        $params = "?timeStamp=".$data["timeStamp"]."&nonceStr=".$data["nonceStr"]."&prepay_id=".$p."&signType=MD5&paySign=".$paySign."&payment_id=".$payment_id."&callback=".$callback_url;

        unset($data['order_id']);

        $strHtml = '
                <html>
                    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                    <title>微信安全支付</title>
                    <script src="https://res.wx.qq.com/open/js/jweixin-1.3.0.js"></script>
                    <script language="javascript">
                    //调用微信JS api 支付

                       wx.config({
                                 debug: false,
                              appId: "'.$appId.'",
                              timestamp: "'.time().'",
                              nonceStr: "'.$nonceStr.'",
                              signature: "'.$paySign.'",
                              jsApiList: []
                       });

                      var params = "'.$params.'";
                      var path = "/pages/wxpay/wxpay"+params;
                      wx.ready(function() {

                          if(window.__wxjs_environment === "miniprogram"){
                              wx.miniProgram.navigateTo({url: path});
                          }
                      });

                    </script>
                    <body>

                 </body>
                </html>
            ';
        return $strHtml;
    }


    protected function get_html(){

      return '';
    }


//↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓公共函数部分↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

    /**
     *  作用：将xml转为array
     */
    public function xmlToArray($xml)
    {
        //将XML转为array
        $array_data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $array_data;
    }


    /**
     *  作用：array转xml
     */
    function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val))
            {
                $xml.="<".$key.">".$val."</".$key.">";
            }
            else
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
        }
        $xml.="</xml>";
        return $xml;
    }

    /**
     *  作用：格式化参数并生成签名
     */
    public function getSign($Parameters, $key)
    {
        ksort($Parameters); //签名步骤一：按字典序排序参数
        $buff = "";
        foreach ($Parameters as $k => $v)
        {
            if($k != "sign" && $v != "" && !is_array($v))
            {
                $buff .= $k . "=" . $v . "&";
            }
        }
        $String = trim($buff, "&");
        $String = $String."&key=".$key; //签名步骤二：在string后加入KEY
		logger::info($String.'stringstringstring');
        $String = md5($String); //签名步骤三：MD5加密
        $result = strtoupper($String); //签名步骤四：所有字符转为大写
        return $result;
    }
	
    /**
     *  作用：以post方式提交xml到对应的接口url
     */
    public function postXmlCurl($xml,$url,$second=30)
    {
        //$res = kernel::single('base_httpclient')->post($url,$xml);
       return $res;
     //初始化curl
     $ch = curl_init();
     //设置超时
     curl_setopt($ch, CURLOPT_TIMEOUT, $second);
     //这里设置代理，如果有的话
     //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
     //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
     curl_setopt($ch,CURLOPT_URL, $url);
     curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
     curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
     //设置header
     curl_setopt($ch, CURLOPT_HEADER, FALSE);
     //要求结果为字符串且输出到屏幕上
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
     //post提交方式
     curl_setopt($ch, CURLOPT_POST, TRUE);
     curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
     //运行curl
     $data = curl_exec($ch);
     curl_close($ch);
     //返回结果
     if($data)
     {
         curl_close($ch);
         return $data;
     }
     else
     {
         $error = curl_errno($ch);
         echo "curl出错，错误码:$error"."<br>";
         echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
         curl_close($ch);
         return false;
     }
    }
    /**
     *  作用：以post方式提交xml到对应的接口url
     */
    public function wxpostXmlCurl($xml,$url,$second=30)
    {
        //$res = kernel::single('base_httpclient')->post($url,$xml);
        //return $res;
	/*根据情况选择需要的方法，有些服务器curl能用，有些要模式*/
	$this->tnetcore = kernel::single('base_http');
 	//$this->netcore = kernel::single('base_curl');

       $res= $this->tnetcore->action('post',$url,NULL,NULL,$xml,false);
	return $res;
//      //初始化curl
//      $ch = curl_init();
//      //设置超时
//      curl_setopt($ch, CURLOPT_TIMEOUT, $second);
//      //这里设置代理，如果有的话
//      //curl_setopt($ch,CURLOPT_PROXY, '8.8.8.8');
//      //curl_setopt($ch,CURLOPT_PROXYPORT, 8080);
//      curl_setopt($ch,CURLOPT_URL, $url);
//      curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
//      curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
//      //设置header
//      curl_setopt($ch, CURLOPT_HEADER, FALSE);
//      //要求结果为字符串且输出到屏幕上
//      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//      //post提交方式
//      curl_setopt($ch, CURLOPT_POST, TRUE);
//      curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
//      //运行curl
//      $data = curl_exec($ch);
//      curl_close($ch);
//      //返回结果
//      if($data)
//      {
//          curl_close($ch);
//          return $data;
//      }
//      else
//      {
//          $error = curl_errno($ch);
//          echo "curl出错，错误码:$error"."<br>";
//          echo "<a href='http://curl.haxx.se/libcurl/c/libcurl-errors.html'>错误原因查询</a></br>";
//          curl_close($ch);
//          return false;
//      }
    }

    /**
     *  作用：设置标配的请求参数，生成签名，生成接口参数xml
     */
    function createXml($parameters)
    {
        $this->parameters["appid"] = WxPayConf_pub::APPID;//公众账号ID
        $this->parameters["mch_id"] = WxPayConf_pub::MCHID;//商户号
        $this->parameters["nonce_str"] = $this->createNoncestr();//随机字符串
        $this->parameters["sign"] = $this->getSign($this->parameters);//签名
        return  $this->arrayToXml($this->parameters);
    }

    /**
     *  作用：post请求xml
     */
    function postXml()
    {
        $xml = $this->createXml();
        $this->response = $this->wxpostXmlCurl($xml,$this->url,$this->curl_timeout);
        return $this->response;
    }

    /**
     *  作用：格式化参数，签名过程需要使用
     */
    function formatBizQueryParaMap($paraMap, $urlencode)
    {
        $buff = "";
        ksort($paraMap);
        foreach ($paraMap as $k => $v)
        {
            if($urlencode)
            {
               $v = urlencode($v);
            }
            //$buff .= strtolower($k) . "=" . $v . "&";
            $buff .= $k . "=" . $v . "&";
        }
        $reqPar;
        if (strlen($buff) > 0)
        {
            $reqPar = substr($buff, 0, strlen($buff)-1);
        }
        return $reqPar;
    }

  

//↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑公共函数部分↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

}
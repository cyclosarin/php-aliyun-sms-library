<?php
namespace Cyclosarin\AliCloudSMS;
use Cyclosarin\AliCloudSMS\Util\Error;

class Client {
    private const DOMAIN = "dysmsapi.aliyuncs.com";
    private const METHOD = "HMAC-SHA1";
    private const VERSION = "1.0";
    private const FORMAT = "json";
    private const REGION_ID = "cn-hangzhou";
    private const SEND_ACTION = "SendSms";
    private const QUERY_ACTION = "QuerySendDetails";
    private const VERSION_DATE = "2017-05-25";
    // query details
    private const CURRENT_PAGE = 1;
    private const PAGE_SIZE = 10;


    private $params = [];
    private $access_key_secret = null;
    private $https = false;

    public function __construct($access_key_id, $access_key_secret, $sign_name, $template_code, $https = false) {
        if ((!isset($access_key_id) || !isset($access_key_secret)) || (!isset($sign_name) || !isset($template_code))) {
            throw new \Exception(Error::SMS_ERROR_PARAMETER);
        }

        $this->access_key_secret = $access_key_secret;
        $this->https = $https;
        $this->params = array(
            "SignatureMethod" => self::METHOD,
            "SignatureNonce" => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => self::VERSION,
            "AccessKeyId" => $access_key_id,
            "Timestamp" => gmdate("Y-m-d\TH:i:s\Z"),
            "Format" => self::FORMAT,
            "SignName" => $sign_name,
            "TemplateCode" => $template_code,
            "RegionId" => self::REGION_ID,
            "Version" => self::VERSION_DATE
        );

        return $this;
    }

    private function encode($value) {
        $value = urlencode($value);
        $value = preg_replace("/\+/", "%20", $value);
        $value = preg_replace("/\*/", "%2A", $value);
        $value = preg_replace("/%7E/", "~", $value);
        return $value;
    }

    private function signature($mobile, $value = array()) {
        if ($this->params['Action'] == self::SEND_ACTION) {
            if (!isset($mobile) && !isset($value) || empty($value)) {
                throw new \Exception(Error::SMS_ERROR_INVAILD_MOBILE);
            }

            $params = array_merge($this->params, array(
                "PhoneNumbers" => $mobile,
                "TemplateParam" => $value
            ));

        } else {
            if (!isset($mobile) || empty($mobile)) {
                throw new \Exception(Error::SMS_ERROR_INVAILD_MOBILE);
            }

            $params = array_merge($this->params, array(
                "PhoneNumber" => $mobile
            ));
        }

        ksort($params);
        $sortedQueryString = "";
        foreach ($params as $key => $param) {
            $sortedQueryString .= "&" . $this->encode($key) . "=" . $this->encode($param);
        }
        $sign = base64_encode(hash_hmac("sha1", "GET&%2F&" . $this->encode(substr($sortedQueryString, 1)), $this->access_key_secret . "&", true));
        $signatureToken = $this->encode($sign);
        $domain = self::DOMAIN;
        $uri = ($this->https ? "https:" : "http:") . "//${domain}/?Signature=${signatureToken}${sortedQueryString}";
        return $uri;
    }

    private function curl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0"
        ));
        if(substr($url, 0,5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }
        $rtn = curl_exec($ch);
        if($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);
        return $rtn;
    }

    public function send($mobile, $value) {
        $this->params = array_merge($this->params, array(
            "Action" => self::SEND_ACTION
        ));

        if (is_array($value)) {
            $value = json_encode($value);
        }

        $uri = $this->signature($mobile, $value);
        try {
            $content = $this->curl($uri);
            $rtn = json_decode($content);

            return [
                "success" => $rtn->Message == 'OK',
                "details" => $rtn
            ];
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function query($mobile, $options = array()) {
        $this->params = array_merge($this->params, array(
            "Action" => self::QUERY_ACTION,
            "SendDate" => @isset($options['date']) ? $options['date'] : gmdate("Ymd"),
            "PageSize" => @isset($options['pageSize']) ? $options['pageSize'] : self::PAGE_SIZE,
            "CurrentPage" => @isset($options['currentPage']) ? $options['currentPage'] : self::CURRENT_PAGE,
        ));

		if (@isset($options['bizId'])) {
            $this->params = array_merge($this->params, array(
                'BizId' => $options['bizId']
            ));
		}

		$uri = $this->signature($mobile);

		try {
            $content = $this->curl($uri);
            $rtn = json_decode($content);

            return [
                "success" => $rtn->Message == 'OK',
                "details" => $rtn
            ];
        } catch (\Exception $e) {
            return $e;
        }
	}
}
> AliCloud SMS Service SDK

## 安装
```bash
$ composer require cyclosarin/aliyun-sms
```

## 使用
```php
<?php
require_once "vendor/autoload.php";

use Cyclosarin\AliCloudSMS\Client;
$client = new Client(
	$accessKeyId,
	$accessKeySecret,
	$SignName,
	$TemplateCode
);

// 发送短信验证码
$result = $client->send("13888888888", ["code" => "123456"]);
// 查询短信验证码（有参数）
$result = $client->query("13888888888", array(
	"bizId" => '', // (optional) 流水账单ID，可选
	"date" => "20121212", // (optional) 格式为: 20131212 gmdate("Ymd")，可选，默认为当天
	"pageSize" => 10, // (optional) 查询每页条数，可选，默认为10
	"currentPage" => 1, // (optional) 当前页面，可选，默认为1
));

// 查询短信验证码（简单无参数），查询当天的最新十条记录
$result = $client->query("13888888888");

// 返回结果:
$result = [
	"success" => (boolean) true || false,
	"message" => [
		"Message" => "OK",
		// more result body...
	]
]
```

## 在 Laravel 中使用
```php
<?php
// config/app.php 文件中注册服务
return [
	//....
	"providers" => [
		//...
		Cyclosarin\AliCloudSMS\Providers\ShortMessageServiceProvider::class
		//...
	]
];

// 设置配置项
// 1. 在 .env 文件中设置
SMS_ACCESS_KEY_ID=...
SMS_ACCESS_KEY_SECRET=...
SMS_TEMPLATE_CODE=...
SMS_SIGN_NAME=...
// 2. 在 services.php 中设置
return [
	'sms' => [
        'id' => env('SMS_ACCESS_KEY_ID', '...'),
        'secret' => env('SMS_ACCESS_KEY_SECRET', '...'),
        'template' => env('SMS_TEMPLATE_CODE', '...'),
        'signal' => env('SMS_SIGN_NAME', '...'),
    ]
]

// Controller 中调用
use Cyclosarin\AliCloudSMS\Client;
class HomeController extends Controller {
	protected $client;
	public function __construct(Client $client) {
		$this->client = $client;
	}

	public function querySmsDetails() {
		$result = $this->client->query("13888888888")
		//...
	}
}
```

## PS: 
1. 获取[AccessKeyId][0]和[AccessKeySecret][0]
2. 获取[短信签名][1]和[短信模版][2]

[0]:https://help.aliyun.com/document_detail/59031.html
[1]:https://help.aliyun.com/document_detail/55327.html
[2]:https://help.aliyun.com/document_detail/55330.html
> AliCloud SMS Service SDK

## Installation
```bash
$ composer req cyclosarin/aliyun-sms
```

## Usage
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

$client->send("13888888888", ["code" => "123456"], $rtn);
// $rtn is results or:
$rtn2 = $client->send("13888888888", ["code" => "123456"]);
```

#### PS: 
1. 获取[AccessKeyId][0]和[AccessKeySecret][0]
2. 获取[短信签名][1]和[短信模版][2]

[0]:https://help.aliyun.com/document_detail/59031.html
[1]:https://help.aliyun.com/document_detail/55327.html
[2]:https://help.aliyun.com/document_detail/55330.html
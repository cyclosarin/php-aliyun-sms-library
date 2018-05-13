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
// $rtn is results or u can:
$rtn2 = $client->send("13888888888", ["code" => "123456"]);
```

[0]:https://help.aliyun.com/document_detail/53045.html
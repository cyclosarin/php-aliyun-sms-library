<?php
namespace Cyclosarin\AliCloudSMS\Providers;

use Cyclosarin\Message\Client;
use Illuminate\Support\ServiceProvider;

class ShortMessageServiceProvider extends ServiceProvider {
	public function register() {
		$this->app->bind(Client::class, function($app) {
			return new Client(
				env('SMS_ACCESS_KEY_ID', $app->config['services']['sms']['id']),
                env('SMS_ACCESS_KEY_SECRET', $app->config['services']['sms']['secret']),
                env('SMS_SIGN_NAME', $app->config['services']['sms']['signal']),
                env('SMS_TEMPLATE_CODE', $app->config['services']['sms']['template'])
            );
		});
	}
}
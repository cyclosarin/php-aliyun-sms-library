<?php
namespace Cyclosarin\AliCloudSMS;

use Cyclosarin\Message\Client;
use Illuminate\Support\ServiceProvider;

class SMSServerProvider extends ServiceProvider {
	public function register() {
		$this->app->singleton(Client::class, function($app) {
			return new Client(config("sms"));
		});
	}
}
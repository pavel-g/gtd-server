<?php

namespace Gtd;

class SessionInit {
	
	public static function init($app) {
		$app->add(new \Slim\Middleware\Session([
			'name' => 'gtd_session',
			'autorefresh' => true,
			'lifetime' => '1 hour'
		]));
		$container = $app->getContainer();
		$container['session'] = function ($c) {
			return new \SlimSession\Helper;
		};
	}
	
}
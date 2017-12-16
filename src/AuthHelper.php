<?php

namespace Gtd;

use \Gtd\Propel\UserQuery;

class AuthHelper {
	
	public static function check($user, $pass) {
		$u = UserQuery::create()->filterByName($user)->findOne();
		return ((boolean) ($u->getPass() === $pass));
	}
	
}
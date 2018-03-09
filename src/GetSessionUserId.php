<?php

namespace Gtd;

trait GetSessionUserId
{
	
	protected function getSession() {
		return $this->container['session'];
	}
	
	protected function getUserId() {
		$session = $this->getSession();
		return $session->get('userid');
	}
	
}

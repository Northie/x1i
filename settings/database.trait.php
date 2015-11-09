<?php

namespace settings;

trait _database {

	private $settings = [];

	protected function readSettings() {
		$settings['default'] = [];

		$settings['default']['type'] = 'mysql';
		$settings['default']['host'] = 'localhost';
		$settings['default']['user'] = 'chris';
		$settings['default']['pass'] = 'XQnSPRDs446T9Kfb';
		$settings['default']['name'] = 'testapp';

		$settings['write'] = [];

		$settings['write']['type'] = 'mysql';
		$settings['write']['host'] = 'master.db.app'; //bypasses load balancer
		$settings['write']['user'] = 'writer';
		$settings['write']['pass'] = 'password';
		$settings['write']['name'] = 'app';

		$settings['read'] = [];

		$settings['read']['type'] = 'mysql';
		$settings['read']['host'] = 'lb.db.app'; //connect through load balancer
		$settings['read']['user'] = 'reader';
		$settings['read']['pass'] = 'password';
		$settings['read']['name'] = 'app';


		$this->settings = &$settings;
	}

}

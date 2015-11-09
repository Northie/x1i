<?php

namespace settings;

trait _general {

	protected $settings = [];

	protected function readSettings() {
		$settings = [];

		//$settings['ENVIRONMENT'] = 'LIVE';
		//$settings['ENVIRONMENT'] = 'STAGING';	//User Acceptance
		//$settings['ENVIRONMENT'] = 'TESTING';	//Quality Assurance
		$settings['ENVIRONMENT'] = 'DEVELOPMENT';

		//Basic Routing is done through Realms
		//the request class will use this to seed the realm value of the registry

		$settings['REALMS'] = [];

		if (isset($_SERVER['WINDIR'])) {

			//for windows dev
			$settings['REALMS']['DEFAULT'] = [
				'DOMAIN' => 'localhost',
				'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] . '/xf2/www',
				'APP_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/xf2/app',
				'WEB_PATH' => '/xf2/www'
			];
		} else {
			//for nginx dev
			$settings['REALMS']['DEFAULT'] = [
				'DOMAIN' => 'local.www.xf2',
				'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
				'APP_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/../',
				'WEB_PATH' => '/'
			];
		}
		$settings['REALMS']['ADMIN'] = [
			'DOMAIN' => 'local.admin.xf2',
			'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'],
			'APP_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/../',
			'WEB_PATH' => '/admin/'
		];
		$settings['REALMS']['CONTROL'] = [
			'DOMAIN' => 'local.control.xf2',
			'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'], //assuming control.domain.tld
			'APP_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/../',
			'WEB_PATH' => '/'
		];
		$settings['REALMS']['API'] = [
			'DOMAIN' => 'local.api.xf2',
			'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'], //assuming control.domain.tld
			'APP_PATH' => $_SERVER['DOCUMENT_ROOT'] . '/../',
			'WEB_PATH' => '/'
		];

		$this->settings = $settings;
	}

}

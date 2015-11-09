<?php

namespace services\data\relational;

/**
 * manage single instances of XF_DMOs
 */
class XF_DBA {

	private static $instance;
	private $settings;
	private $connections = array();

	private function __construct($db) {

	}

	/**
	 * $rs = DB::Load('zest')->Execute($sql,$args)->returnArray();
	 */
	public static function Load($db = 'default') {
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($db);
		}
		return self::$instance->getConnection($db);
	}

	public function getConnection($dsn) {
		
		$this->settings = \settings\Database::Load()->get($dsn);
		
		if (!isset($this->connections[$dsn])) {

			$c = new \services\data\relational\XF_PDO($this->settings);

			$link = $c->Connect();

			$dmo = new \services\data\relational\XF_DMO($link);

			$this->resources[$dsn] = $c;

			$this->connections[$dsn] = $dmo;
		}

		return $this->connections[$dsn];
	}

	//CleanUp
	//DB::Load('zest')->closeConnections();
	public function closeConnections() {
		foreach ($this->resources as $key=> $val) {
			$val->DisConnect();
		}
	}

}

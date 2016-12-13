<?php

namespace utils;

class Tools {

	public static function getRequest() {

		$str = $_SERVER['QUERY_STRING'];

		$str = preg_replace("/_dc=[0-9]+/", "", $str);

		$req = explode("/", $str);
		$module = array_shift($req);
		$action = array_shift($req);

		for ($i = 0; $i < count($req); $i+=2) {
			$_GET[$req[$i]] = $_GET[$i + 1];
		}

		return $_GET;
	}

	public static function generatePassword($len = 8, $selection = 'lower', $removeConfusing = true) {

		$lower = 'abcdefghijklmnopqrstuvwxyz';
		$upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$number = '0123456789';

		$confusing = array('0', '1', '2', '5', 'i', 'l', 'o', 's', 'z', 'I', 'L', 'O', 'S', 'Z');

		switch ($selection) {
			case 'default':
				$str = $lower . $upper . $number;
				break;
			case 'lower':
				$str = $lower . $number;
				break;
			case 'upper':
				$str = $upper . $number;
				break;
			case 'alpha':
				$str = $lower . $number;
				break;
			default:
				$str = $lower . $upper . $number;
				break;
		}

		if ($removeConfusing) {
			$str = str_replace($confusing, '', $str);
		}

		$pw = "";

		for ($i = 0; $i < $len; $i++) {
			$pw.=$str[rand(0, strlen($str) - 1)];
		}

		return $pw;
	}

	public static function hashPassword($plain) {
		$password = new \utils\password;
		$hashed = $password->getHashToStore($plain);
		return $hashed;
	}

	public static function encryptStr($msg, $key) {
		$c = new \utils\Crypto;
		return $c->encrypt($msg, $key);
	}

	public static function decryptStr($msg, $key) {
		$c = new \utils\Crypto;
		return $c->decrypt($msg, $key);
	}

	public static function camel_to_title($str) {
		return
			trim(
			ucwords(
				strtolower(
					preg_replace(
						'/([0-9]+)|([A-Z])/', ' $0', $str
					)
				)
			)
		);
	}

	public static function to_camel_case($str) {

		$str = str_replace('_', ' ', $str);
		$str = strtolower($str);
		$str = ucwords($str);
		$str = str_replace(' ', '', $str);



		$str[0] = strtolower($str[0]);

		return $str;
	}

	public static function setCache($key, $data, $ttl = 3600) {
		return false;
		//get default cache adapter
		apc_store($key, $data, $ttl);
	}

	public static function getCache($key) {
		return false;
		//get default cache adapter
		return apc_fetch($key);
	}

	public static function html_escape($raw_input) {
		return htmlspecialchars($raw_input, ENT_QUOTES | ENT_HTML401, 'UTF-8');
	}

	public static function array2object($array) {
		return json_decode(json_encode($arr));
	}

	public static function object2array($object) {
		return json_decode(json_encode($object), 1);
	}

	public static function cleanHtml($html, $attr_black_list = false, $elem_black_list = false) {
		if (!$attr_black_list || !is_array($attr_black_list)) {
			$attr_black_list = ['onclick'];
		}

		if (!$elem_black_list || !is_array($elem_black_list)) {
			$elem_black_list = ['script', 'iframe'];
		}

		$remove_elems = [];

		$dom = new \DOMDocument();

		@$dom->loadHTML("<html><body>" . $html . "</body></html>");

		$els = $dom->getElementsByTagName('*');


		foreach ($els as $el) {

			foreach ($attr_black_list as $attr) {
				if ($el->hasAttribute($attr)) {
					$el->removeAttribute($attr);
				}
			}

			foreach ($elem_black_list as $elem) {
				if (strtolower($el->nodeName) == $elem) {
					$remove_elems[] = $el;
				}
			}
		}

		foreach ($remove_elems as $r) {
			$r->parentNode->removeChild($r);
		}

		$clean = $dom->saveHtml();

		$tidy_config = [
			'clean'=>true,
			'output-html'=>true,
			'bare'=>true,
			'drop-proprietary-attributes'=>false,
			'fix-uri'=>true,
			'merge-spans'=>false, //ensures editor can work
			'join-styles'=>false,
			'indent'=>true,
			'char-encoding'=>'utf8',
			'force-output'=>true,
			//'quiet'		=>	true,
			'tidy-mark'=>false
		];

		//$tidy = tidy_parse_string($clean,$tidy_config,'UTF8');
		//$tidy->cleanRepair();
		//$clean = (string) $tidy;
		//$fb = new FirePHP();
		//$fb->fb($clean);

		list($start, $trash) = explode("</body>", $clean);

		list($trash, $return) = explode("<body>", $start);

		return $return;
	}

	public static function UUID() {
		//return sha1(microtime(true) . uniqid() . mt_rand(0, mt_getrandmax()));
		//http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid#15875555

		$data = openssl_random_pseudo_bytes(16);
                
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

		$uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));

		return $uuid;
	}

	/**
	 *
	 * @param array $a
	 * @param array $parent
	 * @return string
	 * @see http://stackoverflow.com/questions/17316873/php-array-to-a-ini-file
	 */
	public static function arr2ini(array $a, array $parent = array()) {
		$out = '';
		foreach ($a as $k=> $v) {
			if (is_array($v)) {
				//subsection case
				//merge all the sections into one array...
				$sec = array_merge((array) $parent, (array) $k);
				//add section information to the output
				$out .= '[' . implode('.', $sec) . ']' . PHP_EOL;
				//recursively traverse deeper
				$out .= arr2ini($v, $sec);
			} else {
				//plain key->value case
				$out .= "$k=$v" . PHP_EOL;
			}
		}
		return $out;
	}

	public static function filePathProtect($path) {
		
		$find = ["../","./"];
		$replace = "/";
		
		return str_replace("//","/",str_replace($find,$replace,$path));
	}
        
        public static function looksLikeSerialized($str) {
            $pattern = "/^[aOids]\:[0-9]/";
            $subject = substr($str, 0, 10);
            return preg_match($pattern, $subject);
        }
        
        public static function looksLikeJson($str) {
            $pattern = "/^(\[|\{|[0-9]|\")/";
            $subject = substr($str, 0, 10);
            return preg_match($pattern, $subject);
        }
}

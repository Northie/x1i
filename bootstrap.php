<?php

$requiredConstants = [
	 "Z4_PATH"
	,"Z4_APP_PATH"
	,"Z4_WEB_PATH"
	,"Z4_DAT_PATH"
	,"Z4_APP_NAME"
	,"APP_CLASS_LIST"
];

foreach($requiredConstants as $requiredConstant) {
	if(!defined($requiredConstant)) {
		die('constant '.$requiredConstant.' is not defined');
	}
}

include 'utils/traits/singleton.trait.php';
include 'settings/settings.trait.php';
include 'settings/filelist.settings.php';
include Z4_APP_PATH.'/settings/general.trait.php';
include 'settings/general.settings.php';
include 'utils/autoload/fileFinder.class.php';
include 'utils/autoload/autoload.function.php';
<?php
namespace libs\forms;

class Factory {
	public static function Load($cls) {
		return new $cls($cls);
	}
  
  public static function checkImageFile($link) {
    if (preg_match('/^https?:\/\//i', $link)) {
      $link = str_replace( 'https://secure.365villas.com/home/general-img/', 'https://secure.365villas.com/getimage/custom.php', $link );
      return $link;
    } else {
      return '/libs/phpthumb/phpThumb.php?src=' . $link;
    }
  }
}
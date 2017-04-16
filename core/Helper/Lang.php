<?php
namespace Helper;

class Lang {
	
	private static $langConfig = [
		'lang' => '',
		'langDir' => 'common',
	];
	
	private static $readLangFile = [
	
	];
	
	private static $langArray = [
	
	];
	
	public static function get($langName, $file = null) {
		if(!$file) {
			$file = self::$langConfig['langDir'];
		}
		$langArray = self::readLang($file);
		return getArrayTree($langName, $langArray);
	}
	
	public static function readLang($file) {
		if(isset(self::$readLangFile[$file]) && self::$readLangFile[$file] == true) {
			return self::$langArray[$file];
		} else {
			$fileRealSource = MAIN_PATH . 'Lang'. DIRECTORY_SEPARATOR . self::$langConfig['lang'] . DIRECTORY_SEPARATOR . $file . '.php';
			if(file_exists_case($fileRealSource)) {
				require($fileRealSource);
				self::$langArray[$file] = $lang;
			} else {
				throw new \Uoke\uError('Lang File Can Not read', 500);
			}
			return self::$langArray[$file];
		}
	}
	
	public static function setLangConfig($Config) {
		self::$langConfig = array_merge(self::$langConfig, $Config);
		return;
	}
	
}
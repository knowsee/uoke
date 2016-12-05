<?php
namespace Helper;
/**
 * 文件操作助手类
 * 
 * @author knowsee
 * @copyright (c) 2013, Uoke
 * @example Helper\File::$function
 * @version 1.0
 */
class File {
    
    /*
     * 写入文件
     * 
     * @access public
     * @example Helper\File::writefile('example', 'exa.html', 'data/temp/', array('append' => TRUE,'read' => TRUE));
     * Array[append] 为文件写入是否需要以追加的形式写入， TRUE为是，FALSE为否，默认为是。
     * Array[read] 为是否在写入结束后读取文件， TRUE为是，FALSE为否，默认为否。
     * @param string $string 写入的字符串
     * @param string $filename 写入的文件名
     * @param string $fileDir 写入的文件所在文件夹
     * @param array $doSetting 参数
     * @return bool or string
     */
    
    public static function writeFile($string, $filename, $fileDir, $doSetting = array()) {
        $setting = array(
            'append' => isset($doSetting['append']) ? $doSetting['append'] : TRUE,
            'read' => isset($doSetting['read']) ? $doSetting['read'] : FALSE,
        );
        if(!self::checkdir(MAIN_PATH.$fileDir)) {
            if(!self::makedir(MAIN_PATH.$fileDir)) {
                return FALSE;
            }
        }
        $fileFullPath = MAIN_PATH.$fileDir. $filename;
        $fileReturn = file_put_contents($fileFullPath, $string, $setting['append'] ? FILE_APPEND : FILE_USE_INCLUDE_PATH);
        if ($fileReturn) {
            if ($setting['read']) {
                return file_get_contents($fileFullPath);
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function readFile($fileName, $path) {
		if(self::checkfile(MAIN_PATH.$path.$fileName)) {
			return file_get_contents(MAIN_PATH.$path.'/'.$fileName);
		} else {
			return '';
		}
	}
    
	public static function readLine($fileName, $path, $line = 1) {
		if(self::checkfile(MAIN_PATH.$path.$fileName)) {
			$fileRes = fopen(MAIN_PATH.$path.$fileName, 'r');
			$info = fgets($fileRes, 100);
			fclose($fileRes);
			return $info;
		} else {
			return '';
		}
	}
    
    /*
     * 检查目录合法性
     * 
     * @access public
     * @example Helper\File::checkdir('data/temp/');
     * @param string $dir 目录名
     * @return boolen
     */
    
    public static function checkdir($dir) {
        if (is_dir($dir)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    /*
     * 检查文件合法性
     * 
     * @access public
     * @example Helper\File::checkfile('example.txt');
     * @param string $file 文件名（含目录）
     * @return boolen
     */
    
    public static function checkfile($file) {
        if (is_readable($file)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public static function deleteFile($fileName, $path) {
		if(self::checkfile(MAIN_PATH.$path.$fileName)) {
			return unlink(MAIN_PATH.$path.$fileName);
		} else {
			return false;
		}
	}
    
    /*
     * 建立目录
     * 
     * @access public
     * @example Helper\File::makedir('data/temp/');
     * @param string $dir 目录名
     * @return boolen
     */
    
    public static function makedir($dir) {
        if (!mkdir($dir, 0755, true)) {
            return FALSE;
        } else {
			touch($dir . '/index.html');
            return TRUE;
        }
    }
    
    public static function deldir($dir) {
		if (is_dir($dir)) {
			$dh=opendir($dir);
			while ($file=readdir($dh)) {
				if($file!== "." && $file!== "..") {
				  $fullpath=$dir."/".$file;
				  if(!is_dir($fullpath)) {
					  unlink($fullpath);
				  } else {
					  self::deldir($fullpath);
				  }
				}
			}
			return true;
        } else {
            return false;
        }
    }

}

?>

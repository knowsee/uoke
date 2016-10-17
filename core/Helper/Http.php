<?php
namespace Helper;

/**
 * HTTP/链接 操作类
 *
 * @author knowsee
 */
class Http {

    public static function getUrlInfo($url) {
        $file = '';
        preg_match('/\/([^\/]+\.[a-z]+)[^\/]*$/', $url, $file);
        return array('filename' => $file[1], 'ext' => self::fileExt($file[1]));
    }

    public static function fileExt($filename) {
        return addslashes(strtolower(substr(strrchr($filename, '.'), 1, 10)));
    }
    
    public static function getUrl($url) {
        $url = get_headers($url, true);
        if (preg_match('/200/', $url[0])) {
            return $url;
        } else {
            return $url;
        }
    }

}

?>

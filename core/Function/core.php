<?php
/**
 * Core Function
 *
 * @author Knowsee
 */
function CONFIG($field) {
    return getArrayTree($field, \app::$coreConfig);
}

function setCacheFile($configFile, $cacheFile, $reCache = array()) {
    foreach($configFile as $file) {
        require $file;
    }
    $mergeMain = $reCache['cacheName'];
    unset($reCache['cacheName']);
    if(empty($reCache)) {
        $newArray = array_merge($reCache, $$mergeMain);
    } else {
        $newArray = $$mergeMain;
    }
    $status = file_put_contents($cacheFile.'.php', '<?php $cache = '."'".strpack($newArray)."';");
    if($status === false) {
        new Uoke\uError(E_USER_ERROR,'Runtime cache false');
    }
}

function getCacheFile($cacheFile) {
    $cacheFile = $cacheFile.'.php';
    if(file_exists_case($cacheFile)) {
        return $cacheFile;
    } else {
        return '';
    }
}

function showFileToEve($file) {
    return str_replace(array(SYSTEM_PATH, MAIN_PATH), '', $file);
}

function file_exists_case($filename) {
    if (is_file($filename)) {
        if (IS_WIN) {
            if (basename(realpath($filename)) != basename($filename))
                return false;
        }
        return true;
    }
    return false;
}

function compile($filename) {
    $content = php_strip_whitespace($filename);
    $content = trim(substr($content, 5));
    if ('?>' == substr($content, -2))
        $content = substr($content, 0, -2);
    return $content;
}

/**
 * 根据PHP各种类型变量生成唯一标识号
 * @param mixed $mix 变量
 * @return string
 */
function to_guid_string($mix) {
    if (is_object($mix)) {
        return spl_object_hash($mix);
    } elseif (is_resource($mix)) {
        $mix = get_resource_type($mix) . strval($mix);
    } else {
        $mix = serialize($mix);
    }
    return md5($mix);
}

function getArrayTree($treeArray, $Array) {
    $k = explode('/', $treeArray);
    switch (count($k)) {
        case 1:
            return $Array[$k[0]];
        case 2:
            return $Array[$k[0]][$k[1]];
        case 3:
            return $Array[$k[0]][$k[1]][$k[2]];
        case 4:
            return $Array[$k[1]][$k[1]][$k[2]][$k[3]];
    }
}

function dimplode($array) {
    if (!empty($array)) {
        return "'" . implode("','", is_array($array) ? $array : array($array)) . "'";
    } else {
        return 0;
    }
}

function dmicrotime() {
    return array_sum(explode(' ', microtime()));
}

function dstrlen($str) {
    if (strtolower(CHARSET) != 'utf-8') {
        return strlen($str);
    }
    $count = 0;
    for ($i = 0; $i < strlen($str); $i++) {
        $value = ord($str[$i]);
        if ($value > 127) {
            $count++;
            if ($value >= 192 && $value <= 223)
                $i++;
            elseif ($value >= 224 && $value <= 239)
                $i = $i + 2;
            elseif ($value >= 240 && $value <= 247)
                $i = $i + 3;
        }
        $count++;
    }
    return $count;
}

function strpack($string) {
    if(function_exists('msgpack_pack')) {
        return msgpack_pack($string);
    } else {
        return serialize($string);
    }
}

function strdepack($string) {
    if(function_exists('msgpack_unpack')) {
        return msgpack_unpack($string);
    } else {
        return unserialize($string);
    }
}

function resetArray(&$a) {
    foreach($a as $val) {
        $n[] = $val;
    }
    $a = $n;
}


/**
 * key1=value1&key2=value2转array
 * @param $str key1=value1&key2=value2的字符串
 * @param $$needUrlDecode 是否需要解url编码，默认不需要
 */
function parseQString($str, $needUrlDecode=false){
    $result = array();
    $len = strlen($str);
    $temp = "";
    $curChar = "";
    $key = "";
    $isKey = true;
    $isOpen = false;
    $openName = "\0";

    for($i=0; $i<$len; $i++){
        $curChar = $str[$i];
        if($isOpen){
            if( $curChar == $openName){
                $isOpen = false;
            }
            $temp = $temp . $curChar;
        } elseif ($curChar == "{"){
            $isOpen = true;
            $openName = "}";
            $temp = $temp . $curChar;
        } elseif ($curChar == "["){
            $isOpen = true;
            $openName = "]";
            $temp = $temp . $curChar;
        } elseif ($isKey && $curChar == "="){
            $key = $temp;
            $temp = "";
            $isKey = false;
        } elseif ( $curChar == "&" && !$isOpen){
            putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
            $temp = "";
            $isKey = true;
        } else {
            $temp = $temp . $curChar;
        }
    }
    putKeyValueToDictionary($temp, $isKey, $key, $result, $needUrlDecode);
    return $result;
}


function putKeyValueToDictionary($temp, $isKey, $key, &$result, $needUrlDecode) {
    if ($isKey) {
        $key = $temp;
        if (strlen ( $key ) == 0) {
            return false;
        }
        $result [$key] = "";
    } else {
        if (strlen ( $key ) == 0) {
            return false;
        }
        if ($needUrlDecode)
            $result [$key] = urldecode ( $temp );
        else
            $result [$key] = $temp;
    }
}

/**
 * 字符串转换为 数组
 *
 * @param unknown_type $str
 * @return multitype:unknown
 */
function convertStringToArray($str) {
    return parseQString($str);
}

/**
 * 压缩文件 对应java deflate
 *
 * @param unknown_type $params
 */
function deflate_file(&$params) {
    $logger = LogUtil::getLogger();
    foreach ( $_FILES as $file ) {
        $logger->LogInfo ( "---------处理文件---------" );
        if (file_exists ( $file ['tmp_name'] )) {
            $params ['fileName'] = $file ['name'];

            $file_content = file_get_contents ( $file ['tmp_name'] );
            $file_content_deflate = gzcompress ( $file_content );

            $params ['fileContent'] = base64_encode ( $file_content_deflate );
            $logger->LogInfo ( "压缩后文件内容为>" . base64_encode ( $file_content_deflate ) );
        } else {
            $logger->LogInfo ( ">>>>文件上传失败<<<<<" );
        }
    }
}


/**
 * 讲数组转换为string
 *
 * @param $para 数组
 * @param $sort 是否需要排序
 * @param $encode 是否需要URL编码
 * @return string
 */
function createLinkString($para, $sort, $encode) {
    if($para == NULL || !is_array($para))
        return "";

    $linkString = "";
    if ($sort) {
        $para = argSort ( $para );
    }
    while ( list ( $key, $value ) = each ( $para ) ) {
        if ($encode) {
            $value = urlencode ( $value );
        }
        $linkString .= $key . "=" . $value . "&";
    }
    // 去掉最后一个&字符
    $linkString = substr ( $linkString, 0, count ( $linkString ) - 2 );

    return $linkString;
}

/**
 * 对数组排序
 *
 * @param $para 排序前的数组
 *        	return 排序后的数组
 */
function argSort($para) {
    ksort ( $para );
    reset ( $para );
    return $para;
}
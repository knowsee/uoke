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
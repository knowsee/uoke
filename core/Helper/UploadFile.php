<?php
namespace Helper;
use Uoke\uError;

class UploadFile {

    public static $_files;

    private static function LoadFile() {
        if (self::$_files === null) {
            self::$_files = [];
            if (isset($_FILES) && is_array($_FILES)) {
                foreach ($_FILES as $class => $info) {
                    self::loadFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
                }
            }
        }
        return self::$_files;
    }

    /**
     * @param callable $checkFile
     * callable's return is you want to save file's info
     * @return array
     * @throws uError
     */
    public static function saveAs(callable $checkFile) {
        self::LoadFile();
        $fileArray = $checkFile(self::$_files);
        $fileArray = isset($fileArray['tempName']) ? array($fileArray) : $fileArray;
        $moved = array();
        foreach($fileArray as $k => $file) {
            if ($file['error'] == UPLOAD_ERR_OK && $file['size'] > 0) {
                $file['serverFile'] = self::uploadData($file['name']);
                try {
                    if(!move_uploaded_file($file['tempName'], $file['serverFile'])) {
                        $status = copy($file['tempName'], $file['serverFile']);
                        if($status == false) {
                            throw new uError(E_NOTICE, $file['tempName'].', can not upload to '.$moveFile);
                        }
                    }
                    $moved[] = $file;
                } catch (uError $e) {
                    throw new uError(E_USER_ERROR, $e->getMessage());
                }
            }
        }
        return $moved;
    }

    public static function getBaseName($name) {
        $pathInfo = pathinfo('_' . $name, PATHINFO_FILENAME);
        return mb_substr($pathInfo, 1, mb_strlen($pathInfo, '8bit'), '8bit');
    }

    public static function getExtension($fileName) {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }

    private static function uploadData($fileName) {
        $uploadDir = MAIN_PATH.CONFIG('data/dir').'/'.date('ym_d').'/';
        $dirCheck = File::checkdir($uploadDir);
        if($dirCheck == false) {
            File::makedir($uploadDir);
        }
        return $uploadDir.md5($fileName).date('md').'.'.self::getExtension($fileName);
    }

    private static function loadFilesRecursive($key, $names, $tempNames, $types, $sizes, $errors) {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                self::loadFilesRecursive($key . '[' . $i . ']', $name, $tempNames[$i], $types[$i], $sizes[$i], $errors[$i]);
            }
        } elseif ((int)$errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[$key] = [
                'name' => $names,
                'tempName' => $tempNames,
                'type' => $types,
                'size' => $sizes,
                'error' => $errors,
            ];
        }
    }

}
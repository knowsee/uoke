<?php
namespace Helper;
use Uoke\uError;

class UploadFile {

    public static $_files;

    const FILE_NAME = 'name';
    const FILE_TMP_NAME = 'tempName';
    const FILE_TYPE = 'type';
    const FILE_SIZE = 'size';
    const FILE_ERROR = 'error';
    const FILE_VIEW = 'view';
    
    public static function readAs($checkFile = null) {
        $fileArrayList = self::checkFiles($checkFile);
        if($fileArrayList == false) {
            throw new uError(E_USER_ERROR, 'No Upload Files');
        }
        $readResult = array();
        foreach ($fileArrayList as $k => $file) {
            $readResult[] = array(self::FILE_NAME => $file[self::FILE_NAME], self::FILE_VIEW => File::readFile($file[self::FILE_TMP_NAME]));
        }
        return $readResult;
    }

    /**
     * Save Files
     * @param callable|null $checkFile
     * callable's return is you want to save file's info
     * @return array
     * @throws uError
     */
    public static function saveAs($checkFile = null) {
        $fileArrayList = self::checkFiles($checkFile);
        if($fileArrayList == false) {
            throw new uError(E_USER_ERROR, 'No Upload Files');
        }
        $moved = array();
        foreach ($fileArrayList as $k => $file) {
            if ($file[self::FILE_ERROR] !== UPLOAD_ERR_OK && $file[self::FILE_SIZE] <= 0) {
                throw new uError(E_USER_ERROR, 'Files empty');
            }
            $file['serverFile'] = self::uploadData($file[self::FILE_NAME]);
            try {
                self::moveFile($file[self::FILE_TMP_NAME], $file['serverFile']);
                $moved[] = $file;
            } catch (uError $e) {
                throw new uError(E_USER_ERROR, $e->getMessage());
            }
        }
        return $moved;
    }

    public static function moveFile($orgFile, $tagetFile) {
        if (!move_uploaded_file($orgFile, $tagetFile)) {
            $status = copy($orgFile, $tagetFile);
            if ($status == false) {
                throw new uError(E_NOTICE, $orgFile . ', can not move to ' . $tagetFile);
            }
        }
    }

    public static function getBaseName($name) {
        $pathInfo = pathinfo('_' . $name, PATHINFO_FILENAME);
        return mb_substr($pathInfo, 1, mb_strlen($pathInfo, '8bit'), '8bit');
    }

    public static function getExtension($fileName) {
        return strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    }

    private static function LoadFile() {
        if (self::$_files === null && $_FILES) {
            self::$_files = [];
            foreach ($_FILES as $class => $info) {
                self::loadFilesRecursive($class, $info['name'], $info['tmp_name'], $info['type'], $info['size'], $info['error']);
            }
        }
        return self::$_files;
    }

    private static function checkFiles($checkFile) {
        self::LoadFile();
        if($checkFile == null) {
            return self::$_files;
        }
        $fileArray = array_filter(self::$_files, $checkFile);
        if (empty($fileArray)) {
            return fasle;
        }
        return $fileArray;
    }

    private static function uploadData($fileName) {
        $uploadDir = MAIN_PATH . CONFIG('data/dir') . '/' . date('ym_d') . '/';
        $dirCheck = File::checkdir($uploadDir);
        if ($dirCheck == false) {
            File::makedir($uploadDir);
        }
        return $uploadDir . md5($fileName) . date('md') . '.' . self::getExtension($fileName);
    }

    private static function loadFilesRecursive($key, $names, $tempNames, $types, $sizes, $errors) {
        if (is_array($names)) {
            foreach ($names as $i => $name) {
                self::loadFilesRecursive($key . '[' . $i . ']', $name, $tempNames[$i], $types[$i], $sizes[$i], $errors[$i]);
            }
        } elseif ((int) $errors !== UPLOAD_ERR_NO_FILE) {
            self::$_files[$key] = [
                self::FILE_NAME => $names,
                self::FILE_TMP_NAME => $tempNames,
                self::FILE_TYPE => $types,
                self::FILE_SIZE => $sizes,
                self::FILE_ERROR => $errors,
            ];
        }
    }

}

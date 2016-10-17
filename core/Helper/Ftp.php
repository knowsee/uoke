<?php
namespace Helper;

/**
 * FTP操作类
 *
 * @author chengyi
 */
class Ftp {

    private $link;
    private $dirlink;
    private $errormsg;

    public function __construct($server, $username, $password, $port = 21) {
        $this->link = ftp_connect($server, $port, '30');
        if ($this->link === FALSE) {
            $this->errormsg = "Can't Connect";
        }
        if (FALSE === ftp_login($this->link, $username, $password)) {
            $this->errormsg = "Can't Login";
        }
        ftp_pasv($this->link, true);
    }

    public function colse() {
        return ftp_close($this->link);
    }

    public function put($local_file, $remote_file, $mode = 'FTP_ASCII', $func = 'put') {
        $funcarray = array('put', 'ftp_nb_put');
        $func = in_array($func, $funcarray) ? $func : 'put';
        if (FALSE === $func($this->link, $local_file, $remote_file, $mode)) {
            return FALSE;
        }
        return TRUE;
    }

    public function opendir($dir) {
        if (FALSE === ftp_chdir($this->link, $dir)) {
            $this->errormsg = "Can't change this dir";
            return FALSE;
        }
        return TRUE;
    }

    public function rmdir($dir) {
        if (FALSE === ftp_rmdir($this->link, $dir)) {
            $this->errormsg = "Can't remove this dir";
            return FALSE;
        }
        return TRUE;
    }

    public function chmod($mode = '0755', $file = '') {
        $this->dirlink = $file;
        if (FALSE === ftp_chmod($this->link, $mode, $this->dirlink)) {
            $this->errormsg = "Can't change this File/Dir";
            return FALSE;
        }
        return TRUE;
    }

    public function chmoddir($dir) {
        $this->dirlink = $dir;
        return $this;
    }

    /**
     * 获取FTP指定目录列表
     * 
     * @access public
     * @param mixed $dir 文件夹
     * @return array  返回数组, 内分 [dir] 跟 [file]
     */
    public function getlist($dir = '') {
        $dir = !$dir ? $this->dirlink : $dir;
        $list = ftp_nlist($this->link, $dir);
        if (!$list) {
            $this->errormsg = "Can't list this dir";
            return FALSE;
        } else {
            $dirarray = array();
            foreach ($list as $value) {
                $v = strrchr($value, '/');
                if ($v) {
                    $temp = substr(strrchr($value, '/'), '1');
                    $path = pathinfo($temp);
                    if (isset($path['extension']) && $path['extension']) {
                        $dirarray['file'][] = $temp;
                    } else {
                        $dirarray['dir'][] = $temp;
                    }
                } else {
                    $path = pathinfo($value);
                    if (isset($path['extension']) && $path['extension']) {
                        $dirarray['file'][] = $value;
                    } else {
                        $dirarray['dir'][] = $value;
                    }
                }
            }
        }
        return $dirarray;
    }

    public function pwd() {
        return ftp_pwd($this->link);
    }

    public function get_error() {
        return $this->errormsg;
    }

}

?>
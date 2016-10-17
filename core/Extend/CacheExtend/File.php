<?php declare(strict_types = 1);
namespace CacheExtend;
use Helper\File as HelperFile;
class File {
	private $config = array();

	public function __construct(array $config) {
		$this->config = $config;
		return $this;
	}
	
	public function set(string $table, string $value, int $life = 0) : bool {
		return $this->setFile($this->getFileName($table), $value, $this->getFilePath($table), $life);
	}
	
	public function get(string $table) : array {
		$fileStatus = HelperFile::readLine($this->getFileName($table), $this->getFilePath($table));
		$fileFirstLine = $fileStatus ? explode('|', $fileStatus) : '';
		list($lifeTime, $fileNameSha1, $inClass) = count($fileFirstLine) == 3 ? $fileFirstLine : array('','','');
		if($lifeTime < UNIXTIME) {
			$this->delete($table);
			return array();
		} else {
			$fileInfo = HelperFile::readFile($this->getFileName($table), $this->getFilePath($table));
			$let = str_replace($fileStatus, '', $fileInfo);
			return $let;
		}
	}
	
	public function delete(string $table) {
        HelperFile::deldir($this->getFilePath($table));
	}
	
	public function setByfeild(string $table, string $feild, array $value, int $life = 0) : bool {
		if(!is_array($value)) return false;
		return $this->setFile($this->getFileName($table, $feild), strpack($value), $this->getFilePath($table), $life);
	}
	
	public function getByfeild(string $table, string $feild) : array {
		$fileStatus = HelperFile::readLine($this->getFileName($table, $feild), $this->getFilePath($table));
		$fileFirstLine = $fileStatus ? explode('|', $fileStatus) : '';
		list($lifeTime, $fileNameSha1, $inClass) = count($fileFirstLine) == 3 ? $fileFirstLine : array('','','');
		if($lifeTime < UNIXTIME) {
			$this->deleteByfeild($table, $feild);
			return array();
		} else {
			$fileInfo = HelperFile::readFile($this->getFileName($table, $feild), $this->getFilePath($table));
			$let = strdepack(str_replace($fileStatus, '', $fileInfo));
			return is_array($let) ? $let : array();
		}
	}
	
	public function deleteByfeild(string $table, string $feild) {
        HelperFile::deleteFile($this->getFileName($feild), $this->getFilePath($table));
	}
	
	private function getFilePath(string $table) : string  {
		$md5Table = $table ? preg_replace('/\W/','',base64_encode($table)) : '';
		$path = $this->config['cacheDir'].date('ymd').'/'.$md5Table.'/';
		return $path;
	}
	
	private function getFileName(string $table, string $feild = '') : string  {
		return md5($table.$feild);
	}
	
	private function makeFileFirstLine(int $life, string $fileName) : string {
		return UNIXTIME+$life.'|'.sha1($fileName).'|'.__CLASS__;
	}
	
	private function setFile(string $fileName, string $value, string $path, int $life = 0) : bool {
		$life = $life == 0 ? $this->config['cacheLife'] : $life;
		$fileFirst = self::makeFileFirstLine($life,$fileName);
		$fileFirst = str_pad($fileFirst, 99, '0');
		$fileInfo = $fileFirst.$value;
		$return = HelperFile::writeFile($fileInfo, $fileName, $path, array('append' => false,'read' => false));
		return $return;
	}
}

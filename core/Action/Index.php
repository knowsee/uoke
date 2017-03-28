<?php
namespace Action;
use Uoke\Controller, Helper\UploadFile;
use Services\{Buy, Sell, TradeInfo};
class Index extends Controller {

    public function __construct() {}

    public function Index() {
        $this->display('query');
    }

    public function Test() {
		phpinfo();
    }

    public function TestDo() {
        $f = UploadFile::saveAs(function($tempFile) {
            var_dump($tempFile['f']);
            return $tempFile['f'];
        });
        var_dump($f);
    }
    
}
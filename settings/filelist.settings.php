<?php

namespace settings;

class fileList {
    use \utils\traits\singleton;
    use _settings;
    
    private $classList = [];
     
    private function __construct() {
        $this->includeFileList();
    }
    
    public function includeFileList() {
        require \APP_CLASS_LIST;
        $this->classList = $classlist;        
    }
    
    public function getFileForClass($cls) {
        return $this->classList[$cls];
    }
    
     
}

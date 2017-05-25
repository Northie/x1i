<?php
namespace views;

trait view  {
    
     protected $data;
     protected $path = false;


     public function __construct()
     {
         ;
     }
     
     public function setPath($path) {
         $this->path = trim($path,'/');
     }
     
     public function setData($data)
     {
         $this->data = $data;
     }
     
     public function serve()
     {
         include 'templates/www/index.php';
     }
     
     public function __get($name)
     {
         return $this->data[$name];
     }
}

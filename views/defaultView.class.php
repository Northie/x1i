<?php
namespace views;

class DefaultView implements \views\iView {
    
     protected $data;


     public function __construct()
     {
         ;
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

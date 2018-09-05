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
     
     public function renderPartial($partial,$data=[]) {
         $fc = \settings\registry::Load()->get('FrontController');
         
         $req = $fc->request->getNormalisedRequest();
         
         if($req['module']) {
             $path = implode(\DIRECTORY_SEPARATOR,[X1_APP_PATH,'modules',$req['module'],'contexts',$req['context'],'templates']);
         } else {
             $path = implode(\DIRECTORY_SEPARATOR,[X1_APP_PATH,'contexts',$req['context'],'templates']);
         }
         
         $file = $path.\DIRECTORY_SEPARATOR.$partial.".phtml";
         
         include($file);
     }
}

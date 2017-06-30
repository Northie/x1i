<?php

namespace flow\controllers;

class FrontController {
    
    use \Plugins\helper;

    const CONTEXT_TYPE_FOLDER = 1;
    const CONTEXT_TYPE_DOMAIN = 2;
    const CONTEXT_TYPE_SUBDOMAIN = 4;

    public $filters = ['dispatch', 'action'];
    public $request;
    public $response;
    protected $filterList;

    public function __construct($settings) {

        $this->setContextType($settings['contexts']['type']);
        
        $this->setContexts($settings['contexts']['names']);
        
        $this->setDefaultContext($settings['contexts']['default']);
        
        $this->basePath = $settings['contexts']['base'];
        
        $this->request = new \flow\Request;
        $this->response = new \flow\Response;
    }

    public function setContexts($contexts = false, $default = false, $active = false) {

        $this->before('FrontControllerSetContexts', $this);
        
        $contexts = $contexts ? $contexts : ['default'];

        if (!$contexts) {
            $this->defaultContext = 'default';
            $this->activeContext = 'default';
        }

        foreach ($contexts as $c) {
            $this->contexts[$c] = true;
        }

        if ($default && $this->contexts[$default]) {
            $this->defaultContext = $default;
        }

        if ($active && $this->contexts[$active]) {
            $this->activeContext = $active;
        }
        $this->after('FrontControllerSetContexts', $this);
    }

    public function setContextType($type) {
        $this->contextType = $type;
    }

    public function setDefaultContext($context) {
        $this->defaultContext = $context;
    }

    //called by the app's html/index.php 
    public final function Init() {
        
        $this->before('FrontControllerInit', $this);
        
        $aCmds = preg_split("/\/|\\\/",str_replace($this->basePath,"",$this->request->REQUEST_URI));        
        
        $cmds = [];
        
        foreach($aCmds as $i => $cmd) {
            if($cmd != '') {
                $cmds[] = trim($cmd,"/\\");
            }
        }
        
        if($this->contexts[$cmds[0]]) {
            $context = array_shift($cmds);
        } else {
            $context = 'www';
        }

        $this->request->normalise(['context' => $context]);

        if ($this->moduleExists($cmds[0])) {

            $this->request->normalise(['module' => array_shift($cmds)]);
        }

        if ($cmds[0] == '') {
            $cmds[0] = 'index';
        }
        
        //$this->request->normalise(['endpoint' => $cmds[0]]);
        $this->request->normalise(['endpoint' => array_shift($cmds)]);
          
        $this->request->normaliseQuery($cmds);
        
        $this->notify('requestNormalised');
 
        $this->after('FrontControllerInit', $this);
                
    }

    public function Execute() {
        
        $request = $this->request->getNormalisedRequest();
                
        if($this->moduleExists($request['module'])) {
            
            $module = \modules\factory::Build($request['module']);
            
            if($module->hasContextEndPoint($request['context'],$request['endpoint'])) {
                $this->createModuleEndpoint($module,$request['context'],$request['endpoint']);
            }
        } else {
            $this->createEndpoint($request['context'],$request['endpoint']);
        }
        
        $this->filters = $this->endpoint->getNamedFilterList();
        $this->filterList = \libs\DoublyLinkedList\factory::Build();

        foreach ($this->filters as $f) {
                $_f = '\\flow\\filters\\' . $f . 'Filter';
                $filter = new $_f($this->filterList, $this->request, $this->response);
                $this->filterList->push($f, $filter);
                
                $filter->init();
                
        }
        
        $start = $this->filterList->getFirstNode(true);
        $this->before('FilterListStart', $this);
        $start->in();
        $this->after('FilterListStart', $this);
    }

    public function createEndpoint($context,$endpoint) {

        if ($endpoint == '') {
            $endpoint = 'index';
        }
        
        $endPointClass = "\\endpoints\\".$context."\\".$endpoint;
        
        $this->before('FrontControllerCreateEndpoint', $this,['context'=>$context,'endpoint'=>$endpoint]);
        
        $this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
        $this->request->setEndpoint($this->endpoint);
        
        $this->after('FrontControllerCreateEndpoint', $this);
        
    }
    
    public function createModuleEndpoint($module, $context, $endpoint) {
        $r = new \ReflectionObject($module);
        $ns = $r->getNamespaceName();

        if ($endpoint == '') {
            $endpoint = 'index';
        }

        $endPointClass = "\\".$ns."\\".$context."\\".$endpoint;
        
        $this->before('FrontControllerCreateModuleEndpoint', $this, ['module'=>$module,'ns'=> $ns,'context'=>$context,'endpoint'=>$endpoint]);
        
        $this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
        $this->request->setEndpoint($this->endpoint);
        
        $this->after('FrontControllerCreateModuleEndpoint', $this);
        
    }
    
    public function moduleExists($module) {        
        
        if(in_array($module, $this->request->getModules())) {
            return true;
        }
        
        return false;
    }

    public function getRequest() {
        return $this->request;
    }
    
    public function getResponse() {
        return $this->response;
    }
    
}

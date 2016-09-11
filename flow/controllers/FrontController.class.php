<?php

namespace flow\controllers;

class FrontController {

    const CONTEXT_TYPE_FOLDER = 1;
    const CONTEXT_TYPE_DOMAIN = 2;
    const CONTEXT_TYPE_SUBDOMAIN = 4;

    public $filters = ['test', 'action'];
    public $request;
    public $response;
    protected $filterList;

    public function __construct($settings) {

        \Plugins\Plugins::RegisterPlugins();

        $this->setContextType($settings['contexts']['type']);
        
        $this->setContexts($settings['contexts']['names']);
        
        $this->setDefaultContext($settings['contexts']['default']);
        
        $this->basePath = $settings['contexts']['base'];
        
        $this->request = new \flow\Request;
        $this->response = new \flow\Response;
    }

    public function setContexts($contexts = false, $default = false, $active = false) {

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
    }

    public function setContextType($type) {
        $this->contextType = $type;
    }

    public function setDefaultContext($context) {
        $this->defaultContext = $context;
    }

    //called by the app's html/index.php 
    public final function Init() {
        
        
        
        $aCmds = explode("/",str_replace($this->basePath,"",$this->request->REQUEST_URI));        
        
        $cmds = [];
        
        foreach($aCmds as $i => $cmd) {
            if($cmd != '') {
                $cmds[] = $cmd;
            }
        }
        
        $context = 'www';
        
        
        
        if($this->moduleExists($cmds[0])) {
            $module = \modules\factory::Build(array_shift($cmds));
            
            if($module->hasContextEndPoint($context,$cmds[0])) {    
                $this->createModuleEndpoint($module,$context,$cmds[0]);
            }
        } else {
            $this->createEndpoint($context,$cmds[0]);
        }
        
        $this->filters = $this->endpoint->getNamedFilterList();
        $this->filterList = \libs\DoublyLinkedList\factory::Build();

        foreach ($this->filters as $f) {
                $_f = '\\flow\\filters\\' . $f . 'Filter';
                $filter = new $_f($this->filterList, $this->request, $this->response);
                $this->filterList->push($f, $filter);
                $filter->init();
        }
                
    }

    public function Execute() {
        $start = $this->filterList->getFirstNode(true);
        $start->in();
    }

    public function createEndpoint($context,$endpoint) {

        if ($endpoint == '') {
            $endpoint = 'index';
        }

        $endPointClass = "\\endpoints\\".$context."\\".$endpoint;
        
        $this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
        $this->request->setEndpoint($this->endpoint);
        
    }
    
    public function createModuleEndpoint($module,$context,$endpoint) {
        $r = new \ReflectionObject($module);
        $ns = $r->getNamespaceName();
       

        if ($endpoint == '') {
            $endpoint = 'index';
        }

        $endPointClass = "\\".$ns."\\".$context."\\".$endpoint;
        
        $this->endpoint = \endpoints\factory::Build($endPointClass, $this->request, $this->response, $this->filters);
        $this->request->setEndpoint($this->endpoint);
        
    }
    
    public function moduleExists($module) {        
        
        if(in_array($module, $this->request->getModules())) {
            return true;
        }
        
        return false;
    }

}

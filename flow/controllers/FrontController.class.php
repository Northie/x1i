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

    public function __construct() {

        \Plugins\Plugins::RegisterPlugins();

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

    private function getActiveContext() {

        /*
         * do we want
         * /[default-context]/endpoint
         * /context/endpoint
         * 
         * and
         * 
         * /[default-context]/module/endpoint
         * /context/module/endpoint
         *  
         * [context]/[module]/endpoint/ 
         * [context]/[module]/endpoint/
         * [context]/[module]/endpoint/id
         * [context]/[module]/endpoint/id/linkedType
         * 
         * default context/default endpoint
         * default context/specified endpoint
         * default context/module/default endpoint
         * default context/module/specfied endpoint
         * 
         * specified context/default endpoint
         * specified context/specified endpoint
         * specified context/module/default endpoint
         * specified context/module/specified endpoint 
         */


        switch (true) {
            case($this->activeContext):
                break;
            case($this->contextType == self::CONTEXT_TYPE_FOLDER):
                $normalise = str_replace($this->request->DOCUMENT_ROOT, "", X1_WEB_PATH);

                $req = trim(str_replace($normalise, "", $this->request->REQUEST_URI), "/");

                list($context, $trash) = explode("/", $req);

                if ($this->contexts[$context]) {
                    $this->activeContext = $context;
                } else {
                    $this->activeContext = $this->defaultContext;
                }
                break;
            default:
                $this->activeContext = $this->defaultContext;
        }

        return $this->activeContext;
    }

    public function setDefaultContext($context) {
        $this->defaultContext = $context;
    }

    //called by the app's html/index.php 
    public final function Init() {

        $this->request->setActiveContext($this->getActiveContext());

        $this->createEndpoint();

        $this->request->setEndpoint($this->endpoint);

        $this->filters = $this->request->getEndpoint()->getNamedFilterList();

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

    public function createEndpoint() {
        list($trash, $endpoint) = explode($this->request->context, $this->request->REQUEST_URI);

        if ($endpoint == '') {
            $endpoint = 'index';
        }

        $this->endpoint = \endpoints\factory::Build($this->request->context, $endpoint, $this->request, $this->response, $this->filters);
    }

}

<?php

namespace modules;

abstract class module
{

    protected $contexts;

    public final function __construct()
    {
        
        $class = get_called_class();
        $moduleFile = \settings\fileList::Load()->getFileForClass($class);
        
        
        
        $cacheKey = "context-module-endpoint--".str_replace('\\','-',$class);
        
        $cache = \services\data\cache\factory::Build('couchbase');

        $contexts = $cache->read($cacheKey);

        if ($contexts) {
            $this->contexts = $contexts;
        } else {

            $this->contextDir = realpath(dirname($moduleFile) . DIRECTORY_SEPARATOR . 'contexts');
            
            foreach (scandir($this->contextDir) as $fsItem) {
                if (strpos($fsItem, ".") === 0) {
                    continue;
                }

                if (is_dir($this->contextDir . DIRECTORY_SEPARATOR . $fsItem)) {
                    $this->contexts[$fsItem] = [];
                    foreach (scandir(realpath($this->contextDir . DIRECTORY_SEPARATOR . $fsItem . DIRECTORY_SEPARATOR . 'endpoints')) as $endpoint) {
                        if (strpos($fsItem, ".") === 0) {
                            continue;
                        }
                        if (is_file($this->contextDir . DIRECTORY_SEPARATOR . $fsItem . DIRECTORY_SEPARATOR . 'endpoints' . DIRECTORY_SEPARATOR . $endpoint)) {
                            $endpoint = str_replace(".class.php","",$endpoint);
                            $this->contexts[$fsItem][$endpoint] = true;
                        }
                    }
                }
            }            
            $cache->create($cacheKey, $this->contexts);
        }
    }

    public function hasContext($context)
    {
        return isset($this->contexts[$context]);
    }

    public function hasContextEndPoint($context, $endPoint)
    {   
        return isset($this->contexts[$context][$endPoint]);
    }

}

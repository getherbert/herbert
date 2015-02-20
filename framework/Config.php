<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class Config {
    
    use PluginAccessorTrait;
    
    /**
     * Contains application's configuration array
     * @var array  
     */
    private $container = array();
    
    
    public function __construct(Plugin $plugin){
        $this->container = $plugin->config;
    }
    
    public function offsetSet($key, $value) 
    {
        if(!$this->offsetExists($key))
            $this->container[$key] = $value;
    }

    public function offsetGet($key) 
    {
        return $this->container[$key];
    }   

    public function offsetExists($key)
    {
        return isset($this->container[$key]);
    }

    public function offsetUnset($key)
    {
        unset($this->container[$key]);
    }
    
    private function __wakeup(){}
    private function __clone(){}
}
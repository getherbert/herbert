<?php

namespace Herbert\Framework;

class Controller
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function call($callback, $args = [])
    {
        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback, 2);
            //require_once $this->plugin->config['path']['controllers'] . $controller . '.php';
            $controllerInstance = new $controller($this->plugin);
            if (!empty($args)) {
                echo call_user_func_array([$controllerInstance, $method], $args);
            } else {
                echo $controllerInstance->$method();
            }
        } else {
            if (!empty($args)) {
                echo call_user_func_array($callback, $args);
            } else {
                echo $callback();
            }
        }
    }

    public function fetch($callback, $args = [])
    {
        list($controller, $method) = explode('@', $callback, 2);

        $controllerInstance = new $controller($this->plugin);
        if (!empty($args)) {
            return call_user_func_array([$controllerInstance, $method], $args);
        } else {
            return $controllerInstance->$method();
        }
    }
}

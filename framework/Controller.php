<?php namespace Herbert\Framework;

class Controller {

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Calls a controller's response.
     *
     * @param       $callback
     * @param array $args
     */
    public function call($callback, $args = [])
    {
        echo $this->fetch($callback, $args);
    }

    /**
     * Fetches a controller's response.
     *
     * @param       $callback
     * @param array $args
     * @return mixed
     */
    public function fetch($callback, $args = [])
    {
        if (is_string($callback))
        {
            list($class, $method) = explode('@', $callback, 2);
            $controller = new $class($this->plugin);

            if (!empty($args))
            {
                return call_user_func_array([$controller, $method], $args);
            }

            return $controller->$method();
        }

        if (!empty($args))
        {
            return call_user_func_array($callback, $args);
        }

        return $callback();
    }

}

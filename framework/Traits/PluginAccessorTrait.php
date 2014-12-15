<?php namespace Herbert\Framework\Traits;

/**
 * Trait PluginAccessorTrait
 * @package Herbert\Framework\Traits
 * @property \Herbert\Framework\Message $message
 * @property \Herbert\Framework\Controller $controller
 * @property \Herbert\Framework\General $general
 * @property \Herbert\Framework\Response $response
 * @property \Herbert\Framework\View $view
 * @property \Herbert\Framework\Panel $panel
 * @property \Herbert\Framework\Route $route
 * @property \Herbert\Framework\Http $http
 * @property \Herbert\Framework\Enqueue $enqueue
 * @property \Herbert\Framework\Database $database
 * @property \Herbert\Framework\Shortcode $shortcode
 * @property \Herbert\Framework\Widget $widget
 */
trait PluginAccessorTrait {

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     * @return mixed
     */
    public function __get($prop)
    {
        if (property_exists(get_class($this), $prop))
            return $this->{$prop};

        return $this->plugin->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->plugin->{$prop});
    }

}

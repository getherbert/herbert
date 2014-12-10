<?php

namespace Herbert\Framework;

class BaseWidget extends \WP_Widget
{

    public $plugin;

    public function __construct()
    {

    }

    public function boot($plugin)
    {
        $this->plugin = $plugin;
    }

    public function __get($prop)
    {
        if (property_exists(get_class($this), $prop)) {
            return $this->{$prop};
        }

        return $this->plugin->{$prop};
    }

    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->plugin->{$prop});
    }

    public function widget($args, $instance)
    {
        // outputs the content of the widget
    }

    public function form($instance)
    {
        // outputs the options form on admin
    }

    public function update($new_instance, $old_instance)
    {
        // processes widget options to be saved
    }
}

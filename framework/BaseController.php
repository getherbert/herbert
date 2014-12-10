<?php

namespace Herbert\Framework;

class BaseController
{

    public $plugin;

    public function __construct($plugin)
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
}

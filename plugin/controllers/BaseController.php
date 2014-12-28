<?php

use Herbert\Framework\Plugin;
use Herbert\Framework\Traits\PluginAccessorTrait;

class BaseController {

    use PluginAccessorTrait;

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

}

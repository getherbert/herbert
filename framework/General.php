<?php

namespace Herbert\Framework;

class General
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function activate($callback)
    {
        \register_activation_hook(
            $this->plugin->config['path']['core'],
            function () use ($callback) {
                $this->plugin->controller->call($callback);
            });
    }

    public function deactivate($callback)
    {
        \register_deactivation_hook(
            $this->plugin->config['path']['core'],
            function () use ($callback) {
                $this->plugin->controller->call($callback);
            });
    }
}

<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class General {

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

    /**
     * Registers your callback or controller method to be called
     * when the plugin is activated
     *
     * @param $callback
     */
    public function activate($callback)
    {
        \register_activation_hook($this->config['path']['core'], function () use ($callback)
        {
            $this->controller->call($callback);
        });
    }

    /**
     * Registers your callback or controller method to be called
     * when the plugin is deactivated
     *
     * @param $callback
     */
    public function deactivate($callback)
    {
        \register_deactivation_hook($this->config['path']['core'], function () use ($callback)
        {
            $this->controller->call($callback);
        });
    }
}

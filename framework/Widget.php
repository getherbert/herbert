<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class Widget {

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
     * Registers a widget then calls its boot method after.
     *
     * @param $widget
     */
    public function register($widget)
    {
        require_once $this->config['path']['widgets'] . $widget . '.php';

        \add_action('widgets_init', function () use ($widget) {
            global $wp_widget_factory;

            \register_widget($widget);
            $wp_widget_factory->widgets[$widget]->boot($this->plugin);
        });
    }

}

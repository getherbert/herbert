<?php

namespace Herbert\Framework;

class Widget
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function register($widget)
    {
        $plugin = $this->plugin;
        require_once $this->plugin->config['path']['widgets'] . $widget . '.php';
        \add_action('widgets_init', function () use ($widget, $plugin) {
            global $wp_widget_factory;
            \register_widget($widget);
            $wp_widget_factory->widgets[$widget]->boot($plugin);
        });
    }

}

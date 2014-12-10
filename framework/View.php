<?php

namespace Herbert\Framework;

class View
{

    public $twig;
    public $plugin;

    public function  __construct($plugin)
    {
        $this->plugin = $plugin;
        \Twig_Autoloader::register();
        $loader = new \Twig_Loader_Filesystem($plugin->config['path']['views']);
        $this->twig = new \Twig_Environment($loader, array(
            'cache' => false
        ));
    }

    public function render($path, $attrs)
    {
        $attrs['plugin'] = $this->plugin;
        return $this->twig->render($path . '.twig', $attrs);
    }
}

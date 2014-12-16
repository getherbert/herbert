<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;
use Twig_Autoloader;
use Twig_Environment;
use Twig_Loader_Filesystem;

class View {

    use PluginAccessorTrait;

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    /**
     * @var \Twig_Environment
     */
    public $twig;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem($this->config['path']['views']);
        $this->twig = new Twig_Environment($loader, array(
            'cache' => false
        ));
    }

    /**
     * Render the view using twig
     *
     * @param $path
     * @param $attrs
     * @return string
     */
    public function render($path, $attrs)
    {
        $attrs['plugin'] = $this->plugin;

        return $this->twig->render($path . '.twig', $attrs);
    }
}

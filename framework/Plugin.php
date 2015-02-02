<?php namespace Herbert\Framework;

/**
 * Class Plugin
 * @package Herbert\Framework
 * @property Message $message
 * @property Controller $controller
 * @property General $general
 * @property Response $response
 * @property View $view
 * @property Panel $panel
 * @property Route $route
 * @property Http $http
 * @property Enqueue $enqueue
 * @property Database $database
 * @property Shortcode $shortcode
 * @property Widget $widget
 */
class Plugin {

    /**
     * The instance container.
     *
     * @var array
     */
    protected $container = [];

    /**
     *
     */
    public function __construct()
    {
        $this->configure();
        $this->containDefaults();
        $this->build();
    }

    /**
     * Gets the config.
     */
    protected function configure()
    {
        define('HERBERT_CONFIG', true);
        $config = require_once __DIR__ . '/../config.php';

        $siteUrl = get_site_url();
        $this->siteUrl = rtrim($siteUrl, '/');

        $adminUrl = get_admin_url();
        $this->adminUrl = rtrim($adminUrl, '/');

        $this->name = $config['name'];

        $c = $config;

        $c['path']['base'] = str_replace('framework/Plugin.php', '', __FILE__);
        $c['path']['core'] = $c['path']['base'] . $c['core'];
        $c['path']['plugin'] = $c['path']['base'] . $c['plugin'] . '/';
        $c['path']['controllers'] = $c['path']['plugin'] . 'controllers' . '/';
        $c['path']['views'] = $c['path']['plugin'] . $c['views'] . '/';
        $c['path']['assets'] = $c['path']['plugin'] . $c['assets'] . '/';
        $c['path']['widgets'] = $c['path']['plugin'] . 'widgets' . '/';

        $c['url']['base'] = str_replace('framework/', '', plugin_dir_url(__FILE__));
        $c['url']['plugin'] = $c['url']['base'] . $c['plugin'] . '/';
        $c['url']['assets'] = $c['url']['plugin'] . $c['assets'] . '/';

        $this->config = $c;
    }

    /**
     * Contains all default instances.
     */
    protected function containDefaults()
    {
        $this->contain('message', new Message($this));
        $this->contain('controller', new Controller($this));
        $this->contain('general', new General($this));
        $this->contain('response', new Response($this));
        $this->contain('view', new View($this));
        $this->contain('panel', new Panel($this));
        $this->contain('route', new Route($this));
        $this->contain('http', new Http($this));
        $this->contain('enqueue', new Enqueue($this));
        $this->contain('database', new Database($this));
        $this->contain('shortcode', new Shortcode($this));
        $this->contain('widget', new Widget($this));
    }

    /**
     * Builds the plugin.
     */
    protected function build()
    {
        $plugin = $this;

	    foreach ($this->config['buildFiles'] as $filename) {
            require_once $this->config['path']['plugin'] . $filename;
	    }
    }

    /**
     * Contains an instance.
     *
     * @param $name
     * @param $content
     */
    public function contain($name, $content)
    {
        $this->container[$name] = $content;
    }

    /**
     * Magic getter to bypass referencing plugin.
     *
     * @param $prop
     * @return mixed
     */
    public function __get($prop)
    {
        if (array_key_exists($prop, $this->container))
            return $this->container[$prop];

        return $this->{$prop};
    }

    /**
     * Magic isset to bypass referencing plugin.
     *
     * @param $prop
     * @return mixed
     */
    public function __isset($prop)
    {
        return isset($this->{$prop}) || isset($this->container[$prop]);
    }

}

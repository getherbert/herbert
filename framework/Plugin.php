<?php

namespace Herbert\Framework;

class Plugin
{

    public $controller;
    public $general;
    public $view;
    public $panel;
    public $route;
    public $http;
    public $enqueue;
    public $database;
    public $url;
    public $path;
    public $config;
    public $response;
    public $siteUrl;
    public $adminUrl;
    public $shortcode;
    public $api;
    public $widget;
    public $message;
    public $name;

    public function __construct()
    {
        $this->getConfig();
        $this->assignClasses();
        $this->build($this);
    }

    public function build($plugin)
    {
        require_once $this->config['path']['plugin'] . 'activate.php';
        require_once  $this->config['path']['plugin'] . 'deactivate.php';
        require_once  $this->config['path']['plugin'] . 'panels.php';
        require_once  $this->config['path']['plugin'] . 'routes.php';
        require_once  $this->config['path']['plugin'] . 'enqueue.php';
        require_once  $this->config['path']['plugin'] . 'api.php';
        require_once  $this->config['path']['plugin'] . 'shortcodes.php';
        require_once  $this->config['path']['plugin'] . 'widgets.php';
    }

    private function assignClasses()
    {
        $this->message = new Message($this);
        $this->controller = new Controller($this);
        $this->general = new General($this);
        $this->response = new Response($this);
        $this->view = new View($this);
        $this->panel = new Panel($this);
        $this->route = new Route($this);
        $this->http = new Http($this);
        $this->enqueue = new Enqueue($this);
        $this->database = new Database($this);
        $this->shortcode = new Shortcode($this);
        $this->widget = new Widget($this);
    }

    private function getConfig()
    {
        require_once __DIR__ . '/../config.php';

        $siteUrl = get_site_url();
        $this->siteUrl = rtrim($siteUrl, "/");

        $adminUrl = get_admin_url();
        $this->adminUrl = rtrim($adminUrl, "/");

        $this->name = $config['name'];

        $c = $config;

        $c['path']['base'] = str_replace("framework/Plugin.php", "", __FILE__);
        $c['path']['core'] = $c['path']['base'] . $c['core'];
        $c['path']['plugin'] = $c['path']['base'] . $c['plugin'] . '/';
        $c['path']['controllers'] = $c['path']['plugin'] . 'controllers' . '/';
        $c['path']['views'] = $c['path']['plugin'] . $c['views'] . '/';
        $c['path']['assets'] = $c['path']['plugin'] . $c['assets'] . '/';
        $c['path']['widgets'] = $c['path']['plugin'] . 'widgets' . '/';

        $c['url']['base'] = str_replace("framework/", "", plugin_dir_url(__FILE__));
        $c['url']['plugin'] = $c['url']['base'] . $c['plugin'] . '/';
        $c['url']['assets'] = $c['url']['plugin'] . $c['assets'] . '/';

        $this->config = $c;

    }

}

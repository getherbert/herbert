<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class Route {

    use PluginAccessorTrait;

    /**
     * @var array
     */
    protected static $routeBuilders = [
        'get',
        'post',
        'put',
        'delete'
    ];

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    private $routes;
    private $routeVars;
    private $routeNames;
    private $regex = '/(?::[a-z][a-z0-9_]*)/';

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;

        \add_action('wp_loaded', [$this, 'flushRules']);
        \add_action('init', [$this, 'addRouteTag']);
        \add_action('parse_request', [$this, 'parseRequest']);
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $method
     */
    private function add($attrs, $method)
    {
        $this->routes[$method][$attrs['as']] = $attrs;
        \add_action('init', function () use ($attrs, $method)
        {
            $this->addRoute($attrs, $method);
        });
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $method
     */
    public function addRoute($attrs, $method)
    {
        $uri = preg_replace(
            $this->regex,
            "(.+)",
            $attrs['uri']
        );
        $uri = ltrim($uri, '/');

        $vars = [];
        preg_match_all(
            $this->regex,
            $attrs['uri'],
            $vars
        );

        $this->routeNames[$attrs['as']] = $attrs['uri'];
        $this->routeVars[$method][$attrs['as']] = [];
        $this->routes[$method][$attrs['as']]['vars'] = [];

        $url = 'index.php?route_name=' . $attrs['as'];
        if (isset($vars[0][0]))
        {
            $this->routes[$method][$attrs['as']]['vars'] = $vars[0];

            $queryVars = [];
            $i = 1;
            foreach ($vars[0] as $var) {
                $var = ltrim($var, ':');
                $queryVars[$var] = '$matches[' . $i . ']';
                $this->routeVars[$method][$attrs['as']][] = $var;
                \add_rewrite_tag('%' . $var . '%', '(.+)');
                $i++;
            }

            $queryVars['route_name'] = $attrs['as'];
            $url = 'index.php?' . urldecode(http_build_query($queryVars));
        }

        \add_rewrite_rule(
            '^' . $uri,
            $url,
            'top'
        );
    }

    /**
     * @todo description
     *
     * @param $name
     * @param $method
     * @param $get
     */
    public function processRoute($name, $method, $get)
    {
        if (!$this->routes[$method][$name])
        {
            $uri = $this->routeNames[$name];
            $name = array_search($uri, $this->routeNames);

            if (!$this->routes[$method][$name])
            {
                die('No Route');
            }
        }

        $attrs = $this->routes[$method][$name];
        $vars = $this->routeVars[$method][$name];
        $args = [];
        foreach ($vars as $var)
        {
            $args[$var] = $get[$var];
        }

        $this->controller->call($attrs['uses'], $args);
    }

    /**
     * @todo description
     *
     * @param       $name
     * @param array $args
     * @return string
     */
    public function url($name, $args = [])
    {
        $route = [];
        foreach (['GET', 'POST', 'PUT', 'DELETE'] as $method)
        {
            if (isset($this->routes[$method]))
            {
                $route = $this->routes[$method];

                break;
            }
        }

        if ($route === [])
        {
            return '';
        }

        if (!empty($route['vars']) && !empty($args) && count($route['vars']) === count($args))
        {
            $pairs = array_combine($route['vars'], $args);
            $route['uri'] = strtr($route['uri'], $pairs);
        }

        return $this->siteUrl . $route['uri'];
    }

    /**
     * Builds a route.
     *
     * @param $method
     * @param $attrs
     * @param $callback
     */
    protected function buildRoute($method, $attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->add($attrs, strtoupper($method));
    }

    /**
     * Magic method calling.
     *
     * @param       $method
     * @param array $parameters
     * @return mixed
     */
    public function __call($method, $parameters = [])
    {
        if (method_exists($this, $method))
        {
            return call_user_func_array([$this, $method], $parameters);
        }

        if (in_array($method, self::$routeBuilders))
        {
            return call_user_func_array([$this, 'buildRoute'], array_merge([$method], $parameters));
        }
    }

    /**
     * @todo description
     */
    public function addRouteTag()
    {
        \add_rewrite_tag('%route_name%', '(.+)');
    }

    /**
     * @todo description
     */
    public function flushRules()
    {
        \flush_rewrite_rules();
    }

    /**
     * @todo description
     *
     * @param $wp
     */
    public function parseRequest($wp)
    {
        if (array_key_exists('route_name', $wp->query_vars))
        {
            $name = $wp->query_vars['route_name'];
            $method = $_SERVER['REQUEST_METHOD'];

            $this->processRoute($name, $method, $wp->query_vars);

            die(0);
        }
    }

}

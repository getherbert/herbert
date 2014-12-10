<?php

namespace Herbert\Framework;

class Route
{

    private $plugin;
    private $routes;
    private $routeVars;
    private $routeNames;
    private $regex = '/(?::[a-z][a-z0-9_]*)/';

    public function __construct($plugin)
    {
        $this->plugin = $plugin;

        \add_action('wp_loaded', [$this, 'flushRules']);
        \add_action('init', [$this, 'addRouteTag']);
        \add_action('parse_request', [$this, 'parseRequest']);
    }

    private function add($attrs, $method)
    {
        $this->routes[$method][$attrs['as']] = $attrs;
        \add_action('init', function () use ($attrs, $method) {
            $this->addRoute($attrs, $method);
        });
    }

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
        if (isset($vars[0][0])) {
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

    public function processRoute($name, $method, $get)
    {
        if (!$this->routes[$method][$name]) {
            $uri = $this->routeNames[$name];
            $name = array_search($uri, $this->routeNames);
            if (!$this->routes[$method][$name]) {
                die("No Route");
            }
        }

        $attrs = $this->routes[$method][$name];
        $vars = $this->routeVars[$method][$name];
        $args = [];
        foreach ($vars as $var) {
            $args[$var] = $get[$var];
        }

        $this->plugin->controller->call($attrs['uses'], $args);
    }

    public function url($name, $args = [])
    {
        $route = [];
        if (isset($this->routes['GET'][$name])) {
            $route = $this->routes['GET'][$name];
        } else {
            if (isset($this->routes['POST'][$name])) {
                $route = $this->routes['POST'][$name];
            } else {
                if (isset($this->routes['PUT'][$name])) {
                    $route = $this->routes['PUT'][$name];
                } else {
                    if (isset($this->routes['DELETE'][$name])) {
                        $route = $this->routes['DELETE'][$name];
                    } else {
                        return "";
                    }
                }
            }
        }

        if (!empty($route['vars']) && !empty($args) && count($route['vars']) == count($args)) {
            $pairs = array_combine($route['vars'], $args);
            $route['uri'] = strtr($route['uri'], $pairs);
        }

        return $this->plugin->siteUrl . $route['uri'];
    }

    public function get($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->add($attrs, "GET");
    }

    public function post($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->add($attrs, "POST");
    }

    public function put($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->add($attrs, "PUT");
    }

    public function delete($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->add($attrs, "DELETE");
    }

    public function addRouteTag()
    {
        \add_rewrite_tag('%route_name%', '(.+)');
    }

    public function flushRules()
    {
        \flush_rewrite_rules();
    }

    public function parseRequest($wp)
    {
        if (array_key_exists('route_name', $wp->query_vars)) {
            $name = $wp->query_vars['route_name'];
            $method = $_SERVER['REQUEST_METHOD'];
            $this->processRoute($name, $method, $wp->query_vars);
            die(0);
        }
    }
}

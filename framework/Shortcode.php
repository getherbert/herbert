<?php

namespace Herbert\Framework;

class Shortcode
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function add($shortcode, $api, $args = [])
    {
        $apiInstance = $this->plugin->api;
        \add_shortcode($shortcode, function ($atts) use ($apiInstance, $api, $args) {

            if (!empty($args)) {
                $atts = $this->renameArguments($args, $atts);
            }

            if (!empty($atts)) {
                call_user_func_array([$apiInstance, $api], $atts);
            } else {
                $apiInstance->$api();
            }
        });
    }

    public function renameArguments($arguments, $attributes)
    {
        $output = [];
        array_walk($attributes, function ($value, $key) use ($arguments, &$output) {
            if (isset($arguments[$key])) {
                $output[$arguments[$key]] = $value;
            }
        });
        return $output;
    }

}

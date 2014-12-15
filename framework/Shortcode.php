<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class Shortcode {

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
     * @todo description
     *
     * @param       $shortcode
     * @param       $fn
     * @param array $args
     */
    public function add($shortcode, $fn, $args = [])
    {
        \add_shortcode($shortcode, function ($atts) use ($fn, $args) {
            if (!empty($args))
            {
                $atts = $this->renameArguments($args, $atts);
            }

            call_user_func_array([$this->api, $fn], $atts);
        });
    }

    /**
     * @todo description
     *
     * @param $arguments
     * @param $attributes
     * @return array
     */
    public function renameArguments($arguments, $attributes)
    {
        $output = [];
        array_walk($attributes, function ($value, $key) use ($arguments, &$output) {
            if (!isset($arguments[$key]))
            {
                return;
            }

            $output[$arguments[$key]] = $value;
        });

        return $output;
    }

}

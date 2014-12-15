<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class BaseWidget extends \WP_Widget {

    use PluginAccessorTrait;

    /**
     * @var \Herbert\Framework\Plugin
     */
    public $plugin;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function boot(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @todo description
     *
     * @param $args
     * @param $instance
     */
    public function widget($args, $instance)
    {
        //
    }

    /**
     * @todo description
     *
     * @param $instance
     */
    public function form($instance)
    {
        //
    }

    /**
     * @todo description
     *
     * @param $new_instance
     * @param $old_instance
     */
    public function update($new_instance, $old_instance)
    {
        //
    }

}

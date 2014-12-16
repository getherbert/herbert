<?php namespace Herbert\Framework;

class Message {

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
     * Sets success class referred to as 'updated'
     * in wordpress before calling 'display()'
     *
     * @param $message
     */
    public function success($message)
    {
        $this->display($message, 'updated');
    }

    /**
     * Sets warning/notice class referred to as 'update-nag'
     * in wordpress before calling 'display()'
     *
     * @param $message
     */
    public function warning($message)
    {
        $this->display($message, 'update-nag');
    }

    /**
     * Sets error class referred to as 'error'
     * in wordpress before calling 'display()'
     *
     * @param $message
     */
    public function error($message)
    {
        $this->display($message, 'error');
    }

    /**
     * Outputs a message to the Wordpress admin area
     * using 'add_action'
     *
     * @param $message
     * @param $class
     */
    public function display($message, $class)
    {
        \add_action('admin_notices', function () use ($message, $class)
        {
            echo "<div class='{$class}'><p>{$message}</p></div>";
        });
    }

}

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
     * @todo description
     *
     * @param $message
     */
    public function success($message)
    {
        $this->display($message, 'updated');
    }

    /**
     * @todo description
     *
     * @param $message
     */
    public function warning($message)
    {
        $this->display($message, 'update-nag');
    }

    /**
     * @todo description
     *
     * @param $message
     */
    public function error($message)
    {
        $this->display($message, 'error');
    }

    /**
     * @todo description
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

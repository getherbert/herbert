<?php

namespace Herbert\Framework;

class Message
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function display($message, $class)
    {
        \add_action('admin_notices', function () use ($message, $class) {
            echo "<div class='{$class}'><p>{$message}</p></div>";
        });
    }

    public function success($message)
    {
        $this->display($message, 'updated');
    }

    public function warning($message)
    {
        $this->display($message, 'update-nag');
    }

    public function error($message)
    {
        $this->display($message, 'error');
    }


}

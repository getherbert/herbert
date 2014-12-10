<?php

namespace Herbert\Framework;

class Response
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function json($data)
    {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        return json_encode($data);
    }
}

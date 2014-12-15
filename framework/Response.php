<?php namespace Herbert\Framework;

class Response {

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
     * JSONifies a response.
     *
     * @param $data
     * @return string
     */
    public function json($data)
    {
        if (!headers_sent())
        {
            header('Content-Type: application/json');
        }

        return json_encode($data);
    }

}

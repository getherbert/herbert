<?php namespace Herbert\Framework;

class Http {

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
     * Gets a request parameter.
     *
     * @param        $var
     * @param string $default
     * @return string
     */
    public function get($var, $default = '')
    {
        $res = $this->all();

        if (!isset($res[$var]) || empty($res[$var]))
        {
            return $default;
        }

        return $res[$var];
    }

    /**
     * Check if a request parameter exists.
     *
     * @param $var
     * @return bool
     */
    public function has($var)
    {
        return $this->get($var, null) !== null;
    }

    /**
     * Gets all the request parameters.
     *
     * @return mixed
     */
    public function all()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST' ? $_POST : $_GET;
    }

    /**
     * Gets all PUT request parameters
     *
     * @return array
     */
    public function put()
    {
        $this->parseRawHttpRequest($res = []);

        return $res;
    }

    /**
     * Gets all DELETE request parameters
     *
     * @return array
     */
    public function delete()
    {
        $this->parseRawHttpRequest($res = []);

        return $res;
    }

    /**
     * Parses the raw http output of PUT & DELETE and return
     * an array similar to $_POST
     *
     * @param array $inputBlocks
     */
    private function parseRawHttpRequest(array &$inputBlocks)
    {
        $input = file_get_contents('php://input');

        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        $inputBlocks = preg_split("/-+$boundary/", $input);
        array_pop($inputBlocks);

        foreach ($inputBlocks as $id => $block)
        {
            if (empty($block))
            {
                continue;
            }

            if (strpos($block, 'application/octet-stream') !== false)
            {
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            }
            else
            {
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }

            $inputBlocks[$matches[1]] = $matches[2];
        }
    }

}

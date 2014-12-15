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
     * @todo description
     *
     * @return array
     */
    public function put()
    {
        $this->parseRawHttpRequest($res = []);

        return $res;
    }

    /**
     * @todo description
     *
     * @return array
     */
    public function delete()
    {
        $this->parseRawHttpRequest($res = []);

        return $res;
    }

    /**
     * @todo description
     *
     * @param array $a_data
     */
    private function parseRawHttpRequest(array &$a_data)
    {
        // read incoming data
        $input = file_get_contents('php://input');

        // grab multipart boundary from content type header
        preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);
        $boundary = $matches[1];

        // split content by boundary and get rid of last -- element
        $a_blocks = preg_split("/-+$boundary/", $input);
        array_pop($a_blocks);

        // loop data blocks
        foreach ($a_blocks as $id => $block)
        {
            if (empty($block))
            {
                continue;
            }

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== false)
            {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } // parse all other fields
            else
            {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }

            $a_data[$matches[1]] = $matches[2];
        }
    }

}

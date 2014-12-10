<?php

namespace Herbert\Framework;

class Http
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function get($var, $default = "")
    {
        $res = $_GET;
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $res = $_POST;
        }

        if (!isset($res[$var]) || empty($res[$var])) {
            return $default;
        }

        return $res[$var];
    }

    public function has($var)
    {
        if (!$this->get($var, false)) {
            return true;
        }
        return false;
    }

    public function all()
    {
        $res = $_GET;
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $res = $_POST;
        }
        return $res;
    }

    public function put()
    {
        $res = [];
        $this->parseRawHttpRequest($res);
        return $res;
    }

    public function delete()
    {
        $res = [];
        $this->parseRawHttpRequest($res);
        return $res;
    }

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
        foreach ($a_blocks as $id => $block) {
            if (empty($block)) {
                continue;
            }

            // you'll have to var_dump $block to understand this and maybe replace \n or \r with a visibile char

            // parse uploaded files
            if (strpos($block, 'application/octet-stream') !== false) {
                // match "name", then everything after "stream" (optional) except for prepending newlines
                preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
            } // parse all other fields
            else {
                // match "name" and optional value in between newline sequences
                preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
            }
            $a_data[$matches[1]] = $matches[2];
        }
    }

}

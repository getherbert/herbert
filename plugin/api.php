<?php

class Api extends BaseController {

}

$apiName = $plugin->config['api'];
global $$apiName;
$$apiName = new Api($plugin);
$plugin->api = $$apiName;


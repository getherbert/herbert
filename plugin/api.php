<?php

class Api extends Herbert\Framework\BaseController {



}

$apiName = $plugin->config['api'];
global $$apiName;
$$apiName = new Api($plugin);
$plugin->api = $$apiName;


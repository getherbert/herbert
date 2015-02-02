<?php

if (!defined('HERBERT_CONFIG'))
    die();

return [
    'framework' => 'framework', /** You will need to update the composer.json file if you change this value **/
    'plugin'    => 'plugin', /** You will need to update the composer.json file if you change this value **/
    'views'     => 'views',
    'assets'    => 'assets',
    'core'      => 'plugin.php',
    'api'       => 'myPluginApi',
    'name'      => 'My Plugin',
    'buildFiles'=> [
        'activate.php',
        'deactivate.php',
        'panels.php',
        'routes.php',
        'enqueue.php',
        'api.php',
        'shortcodes.php',
        'widgets.php',
        'customPostTypes.php',
    ],
    'eloquent'   => true
];

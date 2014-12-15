<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Plugin Name
 * Plugin URI:        http://plugin-name.com/
 * Description:       A plugin.
 * Version:           1.0.0
 * Author:            Author
 * Author URI:        http://author.com/
 * License:           MIT
 */

require_once __DIR__ . '/vendor/autoload.php';

// Initialise framework
$plugin = new Herbert\Framework\Plugin();

if ($plugin->config['eloquent'])
{
    $plugin->database->eloquent();
}

if (!get_option('permalink_structure'))
{
    $plugin->message->error($plugin->name . ': Please ensure you have permalinks enabled.');
}

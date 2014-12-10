<?php


namespace Herbert\Framework;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{

    private $plugin;
    public $capsule;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function eloquent()
    {
        global $wpdb;

        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver' => 'mysql',
            'host' => DB_HOST,
            'database' => DB_NAME,
            'username' => DB_USER,
            'password' => DB_PASSWORD,
            'charset' => DB_CHARSET,
            'collation' => 'utf8_unicode_ci',
            'prefix' => $wpdb->prefix
        ]);
        $this->capsule->setAsGlobal();
        $this->capsule->bootEloquent();
    }

}

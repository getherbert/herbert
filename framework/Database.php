<?php namespace Herbert\Framework;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database {

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    /**
     * @var \Illuminate\Database\Capsule\Manager
     */
    protected $capsule;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Boots eloquent.
     */
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

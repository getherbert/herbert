<?php


return [

    /**
     * Auto-load all required files.
     */
    'requires' => [
        __DIR__ . '/customPostTypes.php'
    ],

    /**
     * The routes to auto-load.
     */
    'routes' => [
        __DIR__ . '/app/routes.php'
    ],

    /**
     * The panels to auto-load.
     */
    'panels' => [
        __DIR__ . '/app/panels.php'
    ],

    /**
     * The shortcodes to auto-load.
     */
    'shortcodes' => [
        __DIR__ . '/app/shortcodes.php'
    ],

    /**
     * The widgets to auto-load.
     */
    'widgets' => [
        __DIR__ . '/app/widgets.php'
    ],

    /**
     * The widgets to auto-load.
     */
    'enqueue' => [
        __DIR__ . '/app/enqueue.php'
    ],

    /**
     * The APIs to auto-load.
     */
    'apis' => [
        'MyPlugin' => __DIR__ . '/app/api.php'
    ],

    /**
     * The view paths to register.
     *
     * E.G: 'MyPlugin' => __DIR__ . '/views'
     * can be referenced via @MyPlugin/
     * when rendering a view in twig.
     */
    'views' => [
        'MyPlugin' => __DIR__ . '/resources/views'
    ],

    /**
     * The asset path.
     */
    'assets' => '/resources/assets/'

];

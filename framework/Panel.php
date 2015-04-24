<?php namespace Herbert\Framework;

class Panel {

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    private $panels;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Checks panel type and either calls 'addPanel()'
     * or 'addSubPanel()'. These are added to Wordpress
     * 'add_action'
     *
     * @param $attrs
     * @param $callback
     */
    public function add($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->panels[$attrs['as']] = $attrs;

        \add_action('admin_menu', function () use ($attrs)
        {
            if ($attrs['type'] == 'panel')
            {
                $this->addPanel($attrs);
            }
            elseif ($attrs['type'] == 'subpanel' || $attrs['type'] == 'wp-subpanel')
            {
                $this->addSubpanel($attrs);
            }
        });
    }

    /**
     * Renames default subpanel by adding a new Panel
     * with same slug and the new name.
     *
     * @param $attrs
     */
    public function renameDefaultSubpanel($attrs)
    {
        $defaultAttrs = $this->panels[$attrs['default']];

        $defaultAttrs['title'] = $attrs['title'];
        $defaultAttrs['as'] = 'renameDefaultSubpanel';
        $defaultAttrs['type'] = 'subpanel';
        $defaultAttrs['parent'] = $attrs['default'];

        $this->add($defaultAttrs, '');
    }

    /**
     * Fetches the controller method and other attributes
     * before attaching them to Wordpress 'add_menu_page'
     *
     * @param $attrs
     */
    public function addPanel($attrs)
    {
        $icon = $this->fetchIcon($attrs);

        \add_menu_page(
            $attrs['title'],
            $attrs['title'],
            'manage_options',
            $attrs['slug'],
            function () use ($attrs)
            {
                $this->plugin->controller->call($attrs['uses']);
            }, $icon
        );
    }

    /**
     * Fetches the right icon type. Checks to see if
     * its a relative path, http or dashicons.
     *
     * @param $attrs
     * @return string
     */
    public function fetchIcon($attrs)
    {
        if (!isset($attrs['icon']) || empty($attrs['icon']))
        {
            return '';
        }

        if (substr($attrs['icon'], 0, 9) === "dashicons" || substr($attrs['icon'], 0, 5) === "data:"
            || substr($attrs['icon'], 0, 2) === "//" || $attrs['icon'] == 'none')
        {
            return $attrs['icon'];
        }

        $attrs['icon'] = ltrim($attrs['icon'], '/');
        return $this->plugin->config['url']['assets'] . $attrs['icon'];
    }

    /**
     * First checks the Subpanel parent exists then
     * fetches the controller method and other attributes
     * before attaching them to Wordpress 'add_menu_page'
     *
     * @param $attrs
     */
    public function addSubpanel($attrs)
    {
        if (!isset($attrs['parent']))
        {
            new \WP_Error('broke', __("Subpanel needs a parent defined", null));
        }

        if (!isset($this->panels[$attrs['parent']]['slug']) || $attrs['type'] == 'wp-subpanel')
        {
            new \WP_Error('broke', __("Unknown parent for subpanel", null));
        }

        $topSubpanel = $this->panels[$attrs['parent']]['slug'] === $attrs['slug'];

        $parentSlug = $this->panels[$attrs['parent']]['slug'];

        if ($attrs['type'] == 'wp-subpanel')
        {
            $parentSlug = $attrs['parent'];
        }

        \add_submenu_page(
            $parentSlug,
            $attrs['title'],
            $attrs['title'],
            'manage_options',
            $attrs['slug'],
            function () use ($attrs, $topSubpanel) {
                if (!$topSubpanel)
                {
                    $this->plugin->controller->call($attrs['uses']);
                }
            });
    }

    /**
     * Gets the panels.
     *
     * @return mixed
     */
    public function getPanels()
    {
        return $this->panels;
    }

    /**
     * Return the URL for a panel
     *
     * @param $name
     * @return string
     */
    public function url($name)
    {
        if (!isset($this->panels[$name]))
        {
            return '';
        }

        return $this->plugin->adminUrl . '/admin.php?page=' . $this->panels[$name]['slug'];
    }

}

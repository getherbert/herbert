<?php

namespace Herbert\Framework;

class Panel
{

    private $plugin;
    private $panels;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function add($attrs, $callback)
    {
        $attrs['uses'] = $callback;
        $this->panels[$attrs['as']] = $attrs;
        \add_action('admin_menu', function () use ($attrs) {
            if ($attrs['type'] == 'panel') {
                $this->addPanel($attrs);
            } else {
                if ($attrs['type'] == 'subpanel' || $attrs['type'] == 'wp-subpanel') {
                    $this->addSubpanel($attrs);
                }
            }
        });
    }

    public function renameDefaultSubpanel($attrs)
    {
        $defaultAttrs = $this->panels[$attrs['default']];
        $defaultAttrs['title'] = $attrs['title'];
        $defaultAttrs['as'] = 'renameDefaultSubpanel';
        $defaultAttrs['type'] = 'subpanel';
        $defaultAttrs['parent'] = $attrs['default'];
        $this->add($defaultAttrs, "");
    }

    public function addPanel($attrs)
    {

        $icon = $this->fetchIcon($attrs);

        \add_menu_page(
            $attrs['title'],
            $attrs['title'],
            'manage_options',
            $attrs['slug'],
            function () use ($attrs) {
                $this->plugin->controller->call($attrs['uses']);
            }, $icon);
    }

    public function fetchIcon($attrs)
    {
        if (!isset($attrs['icon']) || empty($attrs['icon'])) {
            return '';
        }

        if (substr($attrs['icon'], 0, 9) === "dashicons"
            || substr($attrs['icon'], 0, 5) === "data:"
            || substr($attrs['icon'], 0, 2) === "//"
            || $attrs['icon'] == 'none'
        ) {
            return $attrs['icon'];
        }

        $attrs['icon'] = ltrim($attrs['icon'], '/');
        return $this->plugin->config['url']['assets'] . $attrs['icon'];
    }

    public function addSubpanel($attrs)
    {
        if (!isset($attrs['parent'])) {
            new \WP_Error('broke', __("Subpanel needs a parent defined", null));
        }

        if (!isset($this->panels[$attrs['parent']]['slug']) || $attrs['type'] == 'wp-subpanel') {
            new \WP_Error('broke', __("Unknown parent for subpanel", null));
        }

        $topSubpanel = false;
        if ($this->panels[$attrs['parent']]['slug'] == $attrs['slug']) {
            $topSubpanel = true;
        }

        $parentSlug = $this->panels[$attrs['parent']]['slug'];

        if ($attrs['type'] == 'wp-subpanel') {
            $parentSlug = $attrs['parent'];
        }

        \add_submenu_page(
            $parentSlug,
            $attrs['title'],
            $attrs['title'],
            'manage_options',
            $attrs['slug'],
            function () use ($attrs, $topSubpanel) {
                if (!$topSubpanel) {
                    $this->plugin->controller->call($attrs['uses']);
                }
            });
    }


    private function getCurrentFilter()
    {
        if (function_exists('current_filter')) {
            return \current_filter();
        } else {
            return 'panel';
        }
    }

    public function getPanels()
    {
        return $this->panels;
    }

    public function url($name)
    {
        $panel = [];
        if (isset($this->panels[$name])) {
            $panel = $this->panels[$name];
        } else {
            return "";
        }
        return $this->plugin->adminUrl . '/admin.php?page=' . $panel['slug'];
    }
}

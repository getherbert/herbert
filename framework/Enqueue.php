<?php namespace Herbert\Framework;

use Herbert\Framework\Traits\PluginAccessorTrait;

class Enqueue {

    use PluginAccessorTrait;

    /**
     * All the filters.
     *
     * @var array
     */
    protected static $filters = [
        'hook',
        'panel',
        'page',
        'post',
        'category',
        'archive',
        'search',
        'postType'
    ];

    /**
     * @var \Herbert\Framework\Plugin
     */
    protected $plugin;

    /**
     * @param \Herbert\Framework\Plugin $plugin
     */
    public function __construct(Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $footer
     */
    public function buildInclude($attrs, $footer)
    {
        if (isset($attrs['filter']) && !empty($attrs['filter']))
        {

            $filterBy = key($attrs['filter']);
            $filterWith = reset($attrs['filter']);

            if (!is_array($filterWith))
            {
                $filterWith = [$filterWith];
            }

            if (!$this->filterBy($filterBy, $attrs, $filterWith))
            {
                return;
            }
        }

        if (substr($attrs['src'], 0, 2) !== "//")
        {
            $attrs['src'] = ltrim($attrs['src'], '/');
            $attrs['src'] = $this->config['url']['assets'] . $attrs['src'];
        }

        if (pathinfo($attrs['src'], PATHINFO_EXTENSION) === 'css')
        {
            \wp_enqueue_style($attrs['as'], $attrs['src']);
        }
        else
        {
            \wp_enqueue_script($attrs['as'], $attrs['src'], [], false, $footer);
        }
    }

    /**
     * Filters by a specific filter.
     *
     * @param $by
     * @param $attrs
     * @param $with
     * @return bool
     */
    protected function filterBy($by, $attrs, $with)
    {
        $method = 'filter' . ucfirst($by);

        if (!method_exists($this, $method))
        {
            return false;
        }

        return $this->{$method}($attrs, $with);
    }

    /**
     * @todo description
     *
     * @param        $attrs
     * @param string $footer
     */
    public function admin($attrs, $footer = 'header')
    {
        \add_action('admin_enqueue_scripts', function ($hook) use ($attrs, $footer)
        {
            $attrs['hook'] = $hook;
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * @todo description
     *
     * @param        $attrs
     * @param string $footer
     */
    public function login($attrs, $footer = 'header')
    {
        \add_action('login_enqueue_scripts', function () use ($attrs, $footer)
        {
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * @todo description
     *
     * @param        $attrs
     * @param string $footer
     */
    public function front($attrs, $footer = 'header')
    {
        \add_action('wp_enqueue_scripts', function () use ($attrs, $footer)
        {
            $this->buildInclude($attrs, $this->setFooterFlag($footer));
        });
    }

    /**
     * @todo description
     *
     * @param $footer
     * @return bool
     */
    protected function setFooterFlag($footer)
    {
        return $footer === 'footer';
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterHook($attrs, $filterWith)
    {
        $hook = $attrs['hook'];

        if ($filterWith[0] === '*')
        {
            return true;
        }

        return array_search($hook, $filterWith) !== null;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterPanel($attrs, $filterWith)
    {
        $panels = $this->plugin->panel->getPanels();

        if ($filterWith[0] === '*') {
            if (empty($_GET['page']))
            {
                return false;
            }

            foreach ($panels as $panel)
            {
                if ($panel['slug'] === $_GET['page'])
                {
                    return true;
                }
            }
        }
        else
        {
            foreach ($filterWith as $filter)
            {
                if ($panels[$filter]['slug'] === $_GET['page'])
                {
                    return true;
                }
            }

        }

        return false;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterPage($attrs, $filterWith)
    {
        if ($filterWith[0] === '*' && \is_page())
        {
            return true;
        }

        foreach ($filterWith as $filter)
        {
            if (\is_page($filter))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterPost($attrs, $filterWith)
    {
        if ($filterWith[0] === '*' && \is_single())
        {
            return true;
        }

        foreach ($filterWith as $filter)
        {
            if (\is_single($filter))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterCategory($attrs, $filterWith)
    {
        if ($filterWith[0] === '*' && \is_category())
        {
            return true;
        }

        foreach ($filterWith as $filter)
        {
            if (\is_category($filter))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterArchive($attrs, $filterWith)
    {
        return \is_archive();
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterSearch($attrs, $filterWith)
    {
        return \is_search();
    }

    /**
     * @todo description
     *
     * @param $attrs
     * @param $filterWith
     * @return bool
     */
    public function filterPostType($attrs, $filterWith)
    {
        return array_search(\get_post_type(), $filterWith) !== null;
    }

}

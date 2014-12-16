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
     * Checks if the enqueue passes any filters applied,
     * then appends the site url to the path before calling
     * wp_enqueue_style or wp_enqueue_script depending on there
     * extension
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
     * Adds the enqueue to add_action related to admin pages
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
     * Adds the enqueue to add_action related to login pages
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
     * Adds the enqueue to add_action related to front pages
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
     * Checks if footer flag is set.
     *
     * @param string $footer
     * @return bool
     */
    protected function setFooterFlag($footer)
    {
        return $footer === 'footer';
    }

    /**
     * Filter by Hook (Wordpress standard panels),
     * if '*' is provided then pass as this means it
     * should work on all admin panels.
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
     * Filter by Panels, if '*' is provided then
     * check this panel exists within the defined
     * panels. Else look for specific panels.
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
     * Filter by Page, if '*' is provided then
     * check we are on a page before passing.
     * Else check all values using 'is_page()'
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
     * Filter by Post, if '*' is provided then
     * check we are on a single before passing.
     * Else check all values using 'is_single()'
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
     * Filter by Category, if '*' is provided then
     * check we are on a category page before passing.
     * Else check all values using 'is_category()'
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
     * Filter by Archive, check if the page is a archive
     * using 'is_archive()'.
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
     * Filter by Search, check if this is a search page
     * using 'is_search()'.
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
     * Filter by Post Type, check all values using
     * 'get_post_type()'. Select all '*' is not
     * supported. In that case you 'filterPost'
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

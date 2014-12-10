<?php

namespace Herbert\Framework;

class Enqueue
{

    private $plugin;

    public function __construct($plugin)
    {
        $this->plugin = $plugin;
    }

    public function buildInclude($attrs, $footer)
    {
        if (isset($attrs['filter']) && !empty($attrs['filter'])) {

            $filterBy = key($attrs['filter']);
            $filterWith = reset($attrs['filter']);

            if (!is_array($filterWith)) {
                $filterWith = [$filterWith];
            }

            $match = false;
            switch ($filterBy) {
                case 'hook':
                    $match = $this->filterHook($attrs, $filterWith);
                    break;
                case 'panel':
                    $match = $this->filterPanel($attrs, $filterWith);
                    break;
                case 'page':
                    $match = $this->filterPage($attrs, $filterWith);
                    break;
                case 'post':
                    $match = $this->filterPost($attrs, $filterWith);
                    break;
                case 'category':
                    $match = $this->filterCategory($attrs, $filterWith);
                    break;
                case 'archive':
                    $match = $this->filterArchive($attrs, $filterWith);
                    break;
                case 'search':
                    $match = $this->filterSearch($attrs, $filterWith);
                    break;
                case 'postType':
                    $match = $this->filterPostType($attrs, $filterWith);
                    break;
            }

            if ($match == false) {
                return;
            }
        }

        if (substr($attrs['src'], 0, 2) !== "//") {
            $attrs['src'] = ltrim($attrs['src'], '/');
            $attrs['src'] = $this->plugin->config['url']['assets'] . $attrs['src'];
        }

        if (pathinfo($attrs['src'], PATHINFO_EXTENSION) == "css") {
            \wp_enqueue_style($attrs['as'], $attrs['src']);
        } else {
            \wp_enqueue_script($attrs['as'], $attrs['src']);
        }
    }

    public function admin($attrs, $footer = 'header')
    {
        $footer = $this->setFooterFlag($footer);
        \add_action('admin_enqueue_scripts', function ($hook) use ($attrs, $footer) {
            $attrs['hook'] = $hook;
            $this->buildInclude($attrs, $footer);
        });
    }

    public function login($attrs, $footer = 'header')
    {
        $footer = $this->setFooterFlag($footer);
        \add_action('login_enqueue_scripts', function () use ($attrs, $footer) {
            $this->buildInclude($attrs, $footer);
        });
    }

    public function front($attrs, $footer = 'header')
    {
        $footer = $this->setFooterFlag($footer);
        \add_action('wp_enqueue_scripts', function () use ($attrs, $footer) {
            $this->buildInclude($attrs, $footer);
        });
    }

    private function setFooterFlag($footer)
    {
        if ($footer == 'footer') {
            return true;
        }
        return false;
    }

    public function filterHook($attrs, $filterWith)
    {
        $hook = $attrs['hook'];

        if ($filterWith[0] == '*') {
            return true;
        }

        foreach ($filterWith as $filter) {
            if ($hook == $filter) {
                return true;
            }
        }

        return false;
    }

    public function filterPanel($attrs, $filterWith)
    {
        $panels = $this->plugin->panel->getPanels();

        if ($filterWith[0] == '*') {
            if (empty($_GET['page'])) {
                return false;
            }

            foreach ($panels as $panel) {
                if ($panel['slug'] == $_GET['page']) {
                    return true;
                }
            }
        } else {
            foreach ($filterWith as $filter) {
                if ($panels[$filter]['slug'] == $_GET['page']) {
                    return true;
                }
            }

        }
        return false;
    }

    public function filterPage($attrs, $filterWith)
    {
        if ($filterWith[0] == '*') {
            if (\is_page()) {
                return true;
            }
        } else {
            foreach ($filterWith as $filter) {
                if (\is_page($filter)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function filterPost($attrs, $filterWith)
    {
        if ($filterWith[0] == '*') {
            if (\is_single()) {
                return true;
            }
        } else {
            foreach ($filterWith as $filter) {
                if (\is_single($filter)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function filterCategory($attrs, $filterWith)
    {
        if ($filterWith[0] == '*') {
            if (\is_category()) {
                return true;
            }
        } else {
            foreach ($filterWith as $filter) {
                if (\is_category($filter)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function filterArchive($attrs, $filterWith)
    {
        if (\is_archive()) {
            return true;
        }
        return false;
    }


    public function filterSearch($attrs, $filterWith)
    {
        if (\is_search()) {
            return true;
        }
        return false;
    }

    public function filterPostType($attrs, $filterWith)
    {
        foreach ($filterWith as $filter) {
            if (get_post_type() == $filter) {
                return true;
            }
        }
        return false;
    }


}

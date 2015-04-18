<?php namespace BaunPlugin\Categories;

use Baun\Plugin;

class Categories extends Plugin
{
    public function init()
    {
        $this->events->addListener('baun.afterGlobals', function($event, $theme) {
            $theme->addGlobal('category_url', $this->config->get('categories.category_url'));
        });
    }
}
<?php namespace BaunPlugin\Categories;

use Baun\Plugin;

/**
 * Class Categories
 *
 * @package BaunPlugin\Categories
 */
class Categories extends Plugin
{
    /**
     * Initialise Categories plugin, adding appropriate event listeners
     */
    public function init()
    {
        $this->events->addListener('baun.afterGlobals', function($event, $theme) {
            $theme->addGlobal('category_url', $this->config->get('categories.category_url'));
        });

        $this->events->addListener('baun.filesToPosts', function($event, $allPosts) {
            $categoriesSetup = new CategoriesHandler($allPosts);
            $this->events->emit('baun.categoriesFound', $categoriesSetup->findCategories());
        });
    }
}
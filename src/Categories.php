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
            $theme->addGlobal('category_url', $this->config->get('plugins-maccath-baun-categories-categories.category_url'));
        });

        $this->events->addListener('baun.filesToPosts', function($event, $allPosts) {
            $categoriesSetup = new CategoriesHandler($this->config, $this->router, $this->events, $this->theme);
            $allCategories = $categoriesSetup->findCategories($allPosts);

            if (!empty($allCategories)) {
                $this->events->emit('baun.categoriesFound', $allCategories);
                $categoriesSetup->addCategoryRoutes($allCategories, $allPosts);
            }
        });
    }
}
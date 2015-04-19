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
        $this->theme->addPath(__DIR__ . '/templates');
        $config = $this->config->get('plugins-maccath-baun-categories-categories');

        $this->events->addListener('baun.afterGlobals', function($event, $theme) use ($config) {
            $theme->addGlobal('category_url', $config['category_url']);
            $theme->addGlobal('categories_title', $config['title']);
        });

        $categoriesHandler = new CategoriesHandler($this->config, $this->router, $this->events, $this->theme);

        $this->events->addListener('baun.filesToPosts', function($event, $allPosts) use ($categoriesHandler) {
            $allCategories = $categoriesHandler->findCategories($allPosts);

            if (!empty($allCategories)) {
                $this->events->emit('baun.categoriesFound', $allCategories);
                $categoriesHandler->addCategoryRoutes($allCategories, $allPosts);
            }
        });

        $this->events->addListener('baun.beforePostRender', function($event, $template, $data) use ($categoriesHandler) {
            $data->info->categorylinks = $categoriesHandler->getCategoriesLinks(array($data));
        });

        $this->events->addListener('baun.filesToNav', function($event, $navigation, $navigationObject) use ($config) {
            if (!$config['exclude_from_nav']) {
                $navigationObject->nodes[] = array(
                    'title' => $config['title'],
                    'url' => $config['category_url'],
                    'active' => $this->router->currentUri() == $config['category_url']
                );
            }
        });
    }
}
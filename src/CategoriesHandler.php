<?php

namespace BaunPlugin\Categories;

use Baun\Interfaces\Events;
use Baun\Interfaces\Router;
use Baun\Interfaces\Theme;
use Dflydev\DotAccessData\Data;

/**
 * Class CategoriesHandler
 *
 * @package BaunPlugin\Categories
 */
class CategoriesHandler
{

    /**
     * @var Data configuration data
     */
    private $config;
    /**
     * @var Router router provider
     */
    private $router;
    /**
     * @var Events events provider
     */
    private $events;
    /**
     * @var Theme theme provider
     */
    private $theme;

    /**
     * @param Data   $config
     * @param Router $router
     * @param Events $events
     * @param Theme  $theme
     */
    public function __construct(Data $config, Router $router, Events $events, Theme $theme)
    {
        $this->config = $config;
        $this->router = $router;
        $this->events = $events;
        $this->theme = $theme;
    }

    /**
     * @param array $posts array of posts to check
     *
     * @return array of category names belonging to the given posts
     */
    public function findCategories($posts)
    {
        $categories = array();

        if (!$posts) {
            return array();
        }

        foreach ($posts as $post) {

            $post = json_decode(json_encode($post), true);

            if (!isset($post['info']['categories']) || !$post['info']['categories']) {
                continue;
            }

            foreach (explode(',', $post['info']['categories']) as $categoryName) {
                $categoryName = trim($categoryName);

                if (in_array($categoryName, $categories) === false) {
                    $categories[] = $categoryName;
                }
            }
        }

        return $categories;
    }

    /**
     * Add routes that correspond to blog categories
     *
     * @param $allCategories
     * @param $allPosts
     */
    public function addCategoryRoutes($allCategories, $allPosts)
    {
        foreach ($allCategories as $category) {
            $this->router->add('GET', $this->getPath($category), function () use ($category, $allPosts) {
                $page = isset($_GET['page']) && $_GET['page'] ? abs(intval($_GET['page'])) : 1;

                $categoryPosts = $this->getPostsForCategory($category, $allPosts);

                $postsPagination = [
                    'total_pages'  => $this->countPages($categoryPosts),
                    'current_page' => $page,
                    'base_url'     => $this->getPath($category)
                ];

                $this->events->emit('baun.beforeBlogRender', $categoryPosts, $postsPagination);

                return $this->theme->render('blog', [
                    'all_posts'  => $categoryPosts,
                    'posts'      => $this->getPostsForPage($categoryPosts, $page),
                    'pagination' => $postsPagination,
                    'current_category' => $category,
                ]);
            });
        }

        $this->router->add('GET',
            $this->config->get('plugins-maccath-baun-categories-categories.category_url'),
            function () use ($allCategories, $allPosts) {
                $categories = array();
                foreach ($allCategories as $category) {
                    $categories[] = array(
                        'name' => $category,
                        'link' => $this->getPath($category),
                        'posts' => $this->getPostsForCategory($category, $allPosts),
                    );
                }

                return $this->theme->render('categories', [
                    'categories' => $categories,
                ]);
            }
        );
    }

    /**
     * @param array $posts the posts whose category links to retrieve
     *
     * @return array of links to the categories belonging to $posts
     */
    public function getCategoriesLinks($posts)
    {
        $categories = $this->findCategories($posts);

        $categoryLinks = array();
        foreach ($categories as $category) {
            $categoryLinks[] = "<a href='{$this->getPath($category)}'>{$category}</a>";
        }

        return $categoryLinks;
    }

    /**
     * @param $category string a category name
     * @param $posts array of posts to search
     *
     * @return array of posts that belong to the given category
     */
    public function getPostsForCategory($category, $posts)
    {
        foreach ($posts as $key => $post) {
            if (in_array($category, $this->findCategories(array($post))) === false) {
                unset($posts[$key]);
            }
        }

        return $posts;
    }

    /**
     * @param $category string a category name
     *
     * @return string path for the given $category name
     */
    private function getPath($category)
    {
        return '/' . $this->config->get('plugins-maccath-baun-categories-categories.category_url') . '/' . $category;
    }

    /**
     * @param $posts array of posts
     * @param $page string a numeric page index
     *
     * @return array the subset of $posts that belong to the page $page
     */
    private function getPostsForPage($posts, $page)
    {
        if (!$posts) {
            return array();
        }

        $postsPerPage = $this->config->get('blog.posts_per_page');
        $totalPages = $this->countPages($posts);

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $chunk = !$page || $page <= 1 ? 0 : $page - 1;

        $pages = array_chunk($posts, $postsPerPage);

        return $pages[$chunk];
    }

    /**
     * @param $posts array of posts
     *
     * @return int the number of pages that $posts will span
     */
    private function countPages($posts)
    {
        if (!$posts) {
            return 0;
        }

        $postsPerPage = $this->config->get('blog.posts_per_page');

        return count(array_chunk($posts, $postsPerPage));
    }
}
<?php

namespace BaunPlugin\Categories;

/**
 * Class CategoriesHandler
 *
 * @package BaunPlugin\Categories
 */
class CategoriesHandler
{
    private $config;
    private $router;
    private $events;
    private $theme;

    /**
     * @param $config
     * @param $router
     * @param $events
     * @param $theme
     */
    public function __construct($config, $router, $events, $theme) {
        $this->config = $config;
        $this->router = $router;
        $this->events = $events;
        $this->theme = $theme;
    }

    /**
     * @param $posts array posts to check
     * @return array of category names belonging to the given posts
     */
    public function findCategories($posts)
    {
        $categories = array();

        if (!$posts) {
            return array();
        }

        foreach ($posts as $post) {
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

                $categoryPosts = array();
                foreach ($allPosts as $post) {
                    if (in_array($category, $this->findCategories(array($post))) !== false) {
                        $categoryPosts[] = $post;
                    }
                }

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
                ]);
            });
        }
    }

    private function getPath($category)
    {
        return $this->config->get('plugins-maccath-baun-categories-categories.category_url') . $category;
    }

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

    private function countPages($posts)
    {
        if (!$posts) {
            return 0;
        }

        $postsPerPage = $this->config->get('blog.posts_per_page');

        return count(array_chunk($posts, $postsPerPage));
    }
}
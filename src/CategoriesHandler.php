<?php

namespace BaunPlugin\Categories;

use Categories;

/**
 * Class CategoriesHandler
 *
 * @package BaunPlugin\Categories
 */
class CategoriesHandler
{
    /**
     * @var array all blog posts
     */
    protected $allPosts;

    /**
     * @param $allPosts
     */
    public function __construct($allPosts) {
        if (!is_array($allPosts)) {
            $this->allPosts = array();
        } else {
            $this->allPosts = $allPosts;
        }
    }

    /**
     * @return array of category names
     */
    public function findCategories()
    {
        $categories = array();

        foreach ($this->allPosts as $post) {
            if (!isset($post['info']['categories']) || !$post['info']['categories']) {
                continue;
            }

            foreach (explode(',', $post['info']['categories']) as $categoryName) {
                $categoryName = trim($categoryName);

                if (!in_array($categoryName, $categories)) {
                    $categories[] = $categoryName;
                }
            }
        }

        return $categories;
    }
}
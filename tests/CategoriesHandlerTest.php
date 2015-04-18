<?php

/**
 * Class CategoriesTest
 */
class CategoriesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Assert that no categories are found when $allPosts is null
     */
    public function testFindCategoriesWhenAllPostsNull()
    {
        $allPosts = null;

        $categoriesSetup = new \BaunPlugin\Categories\CategoriesHandler($allPosts);

        $this->assertEquals(array(), $categoriesSetup->findCategories($allPosts));
    }

    /**
     * Assert that no categories are found when no blog posts exist
     */
    public function testFindCategoriesWhenNoPosts()
    {
        $allPosts = array();

        $categoriesSetup = new \BaunPlugin\Categories\CategoriesHandler($allPosts);

        $this->assertEquals(array(), $categoriesSetup->findCategories($allPosts));
    }

    /**
     * Assert that no categories are found when no blog posts have categories
     */
    public function testFindCategoriesWhenPostsDontHaveCategories()
    {
        $allPosts = array(
            array(
                'info' => array(
                )
            ),
            array()
        );

        $categoriesSetup = new \BaunPlugin\Categories\CategoriesHandler($allPosts);

        $this->assertEquals(array(), $categoriesSetup->findCategories($allPosts));
    }

    /**
     * Assert that the correct categories are found, without duplicates, when blog posts exist with categories
     */
    public function testFindCategories()
    {
        $allPosts = array(
            array(
                'info' => array(
                    'categories' => 'reading, minimalism'
                )
            ),
            array(
                'info' => array(
                    'categories' => 'reading, food'
                )
            ),
            array(
                'info' => array(
                )
            ),
        );

        $categoriesSetup = new \BaunPlugin\Categories\CategoriesHandler($allPosts);

        $this->assertEquals(array('reading', 'minimalism', 'food'), $categoriesSetup->findCategories($allPosts));
    }
}
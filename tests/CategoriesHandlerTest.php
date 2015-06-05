<?php

/**
 * Class CategoriesTest
 */
class CategoriesTest extends PHPUnit_Framework_TestCase
{
    private $categoriesHandler;

    /**
     * Tests set-up
     */
    public function setUp() {
        // Set up dummy configuration
        $config = new Dflydev\DotAccessData\Data(array(
            'plugins-maccath-baun-categories-categories' => array(
                'category_url' => 'categories'
            ),
            'blog' => array(
                'posts_per_page' => '5'
            )
        ));

        $router = $this->getMockBuilder('\Baun\Providers\Router')->disableOriginalConstructor()->getMock();
        $events = $this->getMockBuilder('\Baun\Providers\Events')->disableOriginalConstructor()->getMock();
        $theme  = $this->getMockBuilder('\Baun\Providers\Theme')->disableOriginalConstructor()->getMock();

        $this->categoriesHandler = new \BaunPlugin\Categories\CategoriesHandler($config, $router, $events, $theme);
    }

    /**
     * Assert that no categories are found when no blog posts exist
     */
    public function testFindCategoriesWhenNoPosts()
    {
        $this->assertEquals(array(), $this->categoriesHandler->findCategories(array()));
        $this->assertEquals(array(), $this->categoriesHandler->findCategories(null));
        $this->assertEquals(array(), $this->categoriesHandler->findCategories(false));
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

        $this->assertEquals(array(), $this->categoriesHandler->findCategories($allPosts));
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

        $this->assertEquals(array('reading', 'minimalism', 'food'), $this->categoriesHandler->findCategories($allPosts));
    }

    /**
     * Assert that getPath returns correct path for category name
     */
    public function testGetPathForCategoryName() {
        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPath');
        $method->setAccessible(true);

        $this->assertEquals('/categories/reading', $method->invokeArgs($this->categoriesHandler, array('reading')));
    }

    /**
     * Assert that getPostsForPage returns no posts if no posts exist
     */
    public function testGetPostsForFirstPageWhenNoPosts() {
        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(0, count($method->invokeArgs($this->categoriesHandler, array(array(), 1))));
        $this->assertEquals(0, count($method->invokeArgs($this->categoriesHandler, array(false, 1))));
    }

    /**
     * Assert that getPostsForPage returns 5 posts for the first page
     */
    public function testGetPostsForFirstPage() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(5, count($method->invokeArgs($this->categoriesHandler, array($allPosts, 1))));
    }

    /**
     * Assert that getPostsForPage returns 2 posts for the second page
     */
    public function testGetPostsForSecondPage() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(2, count($method->invokeArgs($this->categoriesHandler, array($allPosts, 2))));
    }

    /**
     * Assert that getPostsForPage returns 2 posts for the last page if fetching a page that doesn't exist
     */
    public function testGetPostsForPageThatDoesntExist() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(2, count($method->invokeArgs($this->categoriesHandler, array($allPosts, 10))));
    }

    /**
     * Assert that getPostsForPage returns 5 posts for the first page if not specifying a page number
     */
    public function testGetPostsForFirstPageIfNoPageSpecified() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(5, count($method->invokeArgs($this->categoriesHandler, array($allPosts, false))));
    }

    /**
     * Assert that getPostsForPage returns 5 posts for the first page if specifying a negative page number
     */
    public function testGetPostsForFirstPageIfNegativePageSpecified() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForPage');
        $method->setAccessible(true);

        $this->assertEquals(5, count($method->invokeArgs($this->categoriesHandler, array($allPosts, -1))));
    }

    /**
     * Assert that countPages returns 0 if no posts exist
     */
    public function testCountPagesForNoPosts() {
        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('countPages');
        $method->setAccessible(true);

        $this->assertEquals(0, $method->invokeArgs($this->categoriesHandler, array(array())));
        $this->assertEquals(0, $method->invokeArgs($this->categoriesHandler, array(false)));
    }

    /**
     * Assert that countPages returns 2 if 5 posts per page and 7 posts
     */
    public function testCountPagesFor7Posts5PerPage() {
        $allPosts = array(
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
            array(),
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('countPages');
        $method->setAccessible(true);

        $this->assertEquals(2, $method->invokeArgs($this->categoriesHandler, array($allPosts)));
    }

    /**
     * Assert that getting posts for the 'reading' category returns all posts assigned to 'reading' category
     */
    public function testAssignPostsToCategories() {
        $allPosts = array(
            array(
                'title' => 'one',
                'info' => array(
                    'categories' => 'reading, minimalism'
                )
            ),
            array(
                'title' => 'two',
                'info' => array(
                    'categories' => 'reading, food'
                )
            ),
            array(
                'title' => 'three',
                'info' => array(
                )
            ),
        );

        $readingPosts = array(
            array(
                'title' => 'one',
                'info' => array(
                    'categories' => 'reading, minimalism'
                )
            ),
            array(
                'title' => 'two',
                'info' => array(
                    'categories' => 'reading, food'
                )
            )
        );

        $reflector = new ReflectionClass('\BaunPlugin\Categories\CategoriesHandler');
        $method = $reflector->getMethod('getPostsForCategory');
        $method->setAccessible(true);

        $this->assertEquals($readingPosts, $method->invokeArgs($this->categoriesHandler, array('reading', $allPosts)));
    }
}
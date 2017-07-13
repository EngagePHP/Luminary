<?php

use Luminary\Services\ApiResponse\ResponseHelper;

class ApiResponseHelperTest extends TestCase
{
    /**
     * The root URL for the request
     *
     * @var string
     */
    protected $root;

    /**
     * The url path for the request
     *
     * @var string
     */
    protected $path = 'customers/75/users';

    /**
     * The url query for the request
     *
     * @var string
     */
    protected $query = 'include=location&fields[location]=name';

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->get($this->path . '?' . $this->query);
        $this->root = ResponseHelper::root();
    }

    /**
     * Test the generation of urls
     *
     * @return void
     */
    public function testGenerateUrlMethod()
    {
        $path = explode('/', $this->path);

        $this->assertEquals($this->root . '/' . $this->path, ResponseHelper::generateUrl($path));
    }

    /**
     * Test the root self method returns
     * the full url including the query
     *
     * @return void
     */
    public function testSelfMethod()
    {
        $this->assertEquals($this->root . '/' . $this->path . '?fields%5Blocation%5D=name&include=location', ResponseHelper::self());
    }

    /**
     * Test the URL method returns the url
     * without the query string
     *
     * @return void
     */
    public function testUrlMethod()
    {
        $this->assertEquals($this->root . '/' . $this->path, ResponseHelper::url());
    }

    /**
     * Test the helper returns the correct
     * resource
     *
     * @return void
     */
    public function testResourceMethod()
    {
        $this->assertEquals('customers', ResponseHelper::resource());
    }

    /**
     * Test the resource self method link
     *
     * @return void
     */
    public function testResourceSelfMethod()
    {
        $this->assertEquals($this->root . '/customers/75', ResponseHelper::resourceSelf(75));
    }

    /**
     * Test the relationship link generator
     *
     * @return void
     */
    public function testRelationshipLinkGeneration()
    {
        $singular = ResponseHelper::generateRelationshipLinks('customers', 75, 'users', false);
        $plural = ResponseHelper::generateRelationshipLinks('customers',75, 'users');

        $expectedSingular = [
            'self' => $this->root . '/customers/75/relationships/user',
            'related' => $this->root . '/customers/75/user'
        ];

        $expectedPlural = [
            'self' => $this->root . '/customers/75/relationships/users',
            'related' => $this->root . '/customers/75/users'
        ];

        $this->assertEquals($expectedSingular, $singular);
        $this->assertEquals($expectedPlural, $plural);
    }
}

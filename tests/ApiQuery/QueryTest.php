<?php

use Luminary\Services\ApiQuery\QueryCollection;

class QueryTest extends TestCase
{
    use Luminary\Services\Testing\BaseQueryTrait;

    /**
     * Query String parameters to build
     *
     * @var array
     */
    protected $queryString = [];

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->setUpQuery();
    }

    /**
     * Test the setQuery Method
     *
     * @return void
     */
    public function testBulkQuerySet()
    {
        $query = [
            'fields' => null,
            'filters' => [],
            'include' => 'customers, locations'
        ];

        $query = $this->query->setQuery($query);
        $this->assertCount(1, $query->toArray());
    }

    /**
     * Test the return of getQuery method
     * is always a collection by default
     *
     * @return void
     */
    public function testGetQueryShouldAlwaysBeACollection()
    {
        $query = $this->query->getQuery();

        $this->assertInstanceOf(QueryCollection::class, $query);
        $this->assertCount(0, $query->toArray());
    }
}

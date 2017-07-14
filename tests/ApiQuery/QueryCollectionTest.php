<?php

use Luminary\Services\ApiQuery\QueryCollection;

class QueryCollectionTest extends TestCase
{
    /**
     * Test the return of includes query
     * is always an array
     *
     * @return void
     */
    public function testQueryIncludes()
    {
        $query = new QueryCollection;

        $this->assertCount(2, $query->make(['include' => 'customers, locations'])->include());
        $this->assertCount(0, $query->make(['include' => ''])->include());
        $this->assertCount(0, $query->make()->include());
    }

    /**
     * Test the fields query
     *
     * @return void
     */
    public function testQueryFields()
    {
        $query = new QueryCollection([
            'fields' => [
                'users' => 'id, first_name',
                'customers' => 'id, name'
            ]
        ]);

        $this->assertEquals(['users', 'customers'], array_keys($query->fields()));
        $this->assertEquals(['id', 'first_name'], $query->fields('users'));
        $this->assertEquals(['*'], $query->fields('non-existent'));
        $this->assertEquals(['*'], $query->make()->fields());
    }

    /**
     * Test the fields query
     *
     * @return void
     */
    public function testQueryFilters()
    {
        $query = [
            'resource' => 'users',
            'filter' => [
                'users' => [
                    'and' => [
                        ['first_name', '=', 'john'],
                        ['email', '=', 'test@example.com']
                    ],
                ],
                'customers' => [
                    'and' => [
                        ['name', '=', 'company name'],
                        ['id', '=', 1234]
                    ]
                ]
            ]
        ];

        $expected = [
            'customers' => [
                'and' => [
                    [
                        'attribute' => 'name',
                        'operator' => '=',
                        'value' => 'company name',
                        'type' => 'and',
                    ],
                    [
                        'attribute' => 'id',
                        'operator' => '=',
                        'value' => 1234,
                        'type' => 'and',
                    ]
                ]
            ],
            'users' => [
                'and' => [
                    [
                        'attribute' => 'first_name',
                        'operator' => '=',
                        'value' => 'john',
                        'type' => 'and',
                    ],
                    [
                        'attribute' => 'email',
                        'operator' => '=',
                        'value' => 'test@example.com',
                        'type' => 'and',
                    ]
                ]
            ]
        ];

        $collection = new QueryCollection($query);
        $this->assertEquals($expected, $collection->filters());
        $this->assertEquals(array_get($expected, 'users'), $collection->filters('users'));
        $this->assertEquals([], $collection->filters('non-existent'));
        $this->assertEquals([], $collection->make()->filters());
    }

    /**
     * Test the Paginate Query
     *
     * @return void
     */
    public function testQueryPagination()
    {
        $query = [
            'paginate' => [
                'number' => 2,
                'size' => 3
            ]
        ];

        $collection = new QueryCollection($query);

        // Check that the collection returns the correct page and per page
        $this->assertEquals(['page' => 2, 'per_page' => 3], $collection->pagination());

        // If the pagination is empty, should return an empty array
        $this->assertEquals([], $collection->make()->pagination());
    }

    /**
     * Test mutiple sorting query
     *
     * @return void
     */
    public function testQuerySortMultidimensional()
    {
        $query = [
            'resource' => 'calendar',
            'sort' => [
                'id',
                'year',
                '-users.first_name',
                'users.id',
                'customers.name',
                '-customers.id',
                'customers.users.first_name',
                'customers.users.location.name'
            ]
        ];

        $expected = [
            'calendar' => [
                'id' => 'ASC',
                'year' => 'ASC'
            ],
            'users' => [
                'first_name' => 'DESC',
                'id' => 'ASC'
            ],
            'customers' => [
                'name' => 'ASC',
                'id' => 'DESC',
                'users' => [
                    'first_name' => 'ASC',
                    'location' => [
                        'name' => 'ASC'
                    ]
                ]
            ]
        ];

        $results = new QueryCollection($query);
        $this->assertEquals($expected, $results->sorting());
        $this->assertEquals(array_get($expected, 'calendar'), $results->sorting('calendar'));
        $this->assertEquals(array_except(array_get($expected, 'customers.users'), ['location']), $results->sorting('customers.users'));
        $this->assertEquals([], $results->sorting('test'));
        $this->assertEquals([], $results->make()->sorting());
    }
}

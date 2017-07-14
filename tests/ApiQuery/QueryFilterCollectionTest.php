<?php

use Luminary\Services\ApiQuery\Filters\Collection as FilterCollection;

class QueryFilterCollectionTest extends TestCase
{
    /**
     * Test that the parse method returns
     * the correct array
     *
     * @return void
     */
    public function testDefaultFilterParse()
    {
        $filters = [
            'users' => [
                'and' => [['first_name','bob']],
                'or' => [['first_name','bill']]
            ],
            'customer' => [
                'and' => [['name','evil corp']]
            ],
            ['title', '!=', 'manager'],
            ['other', '>', 0],
            'and' => [['last_name', 'smith']],
            'or' => [['last_name', 'doe']]
        ];

        $expected = [
            'users' => [
                'and' => [
                    ['first_name','bob'],
                    ['last_name', 'smith'],
                    ['title', '!=', 'manager'],
                    ['other', '>', 0]
                ],
                'or' => [
                    ['first_name','bill'],
                    ['last_name', 'doe']
                ]
            ],
            'customer' => [
                'and' => [['name','evil corp']]
            ],
        ];

        $collection = new FilterCollection($filters, 'users');

        // Check that the parsed are returned as a collection
        $this->assertInstanceOf(FilterCollection::class, $collection);

        // Check that we return the expected values
        $this->assertEquals($expected, $collection->toArray());
    }
}

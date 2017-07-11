<?php

use Luminary\Services\ApiQuery\Sorting\Parser;

class QuerySortingParserTest extends TestCase
{
    /**
     * The parser instance
     *
     * @var Luminary\Services\ApiQuery\Sorting\Parser
     */
    protected $parser;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->parser = new Parser;
    }

    /**
     * Test an ASC Sorting Query
     *
     * @return void
     */
    public function testSortingParserASC()
    {
        $parser = $this->parser;

        $query = ['id'];

        $expected = [
            'default' => [
                'id' => 'ASC'
            ]
        ];

        $results = $parser->parse($query);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test an DESC Sorting Query
     *
     * @return void
     */
    public function testSortingParserDESC()
    {
        $parser = $this->parser;

        $query = ['-id'];

        $expected = [
            'users' => [
                'id' => 'DESC'
            ]
        ];

        $results = $parser->parse($query, 'users');

        $this->assertEquals($expected, $results);
    }

    /**
     * Test an DESC Sorting Query
     *
     * @return void
     */
    public function testSortingParserNested()
    {
        $parser = $this->parser;

        $query = [
            '-id',
            'users.id',
            'customers.users.location',
            '-customers.users.id'
        ];

        $expected = [
            'default' => [
                'id' => 'DESC'
            ],
            'users' => [
                'id' => 'ASC'
            ],
            'customers' => [
                'users' => [
                    'location' => 'ASC',
                    'id' => 'DESC'
                ]
            ]
        ];

        $results = $parser->parse($query);

        $this->assertEquals($expected, $results);
    }
}

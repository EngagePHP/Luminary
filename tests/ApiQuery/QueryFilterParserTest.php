<?php

use Luminary\Services\ApiQuery\Filters\Parser;

class QueryFilterParserTest extends TestCase
{
    /**
     * The parser instance
     *
     * @var Luminary\Services\ApiQuery\Filters\Parser
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
     * Test that get type method returns a list
     * of all filter types or just a subset
     * of filter types
     *
     * @return void
     */
    public function testGetTypeMethod()
    {
        $parser = $this->parser;
        $filters = $parser->getQueryTypes();

        // Check the keys should be the same
        $this->assertEquals(['and', 'or', 'between', 'or_between', 'nested', 'or_nested', 'has'], $filters);

        // Return requested query types except those given
        $this->assertEquals(['and', 'between', 'or_between', 'or_nested', 'has'], $parser->getQueryTypes(['or', 'nested']));
    }

    /**
     * Test the return of a parsed `AND` query
     *
     * @return void
     */
    public function testParseAndQuery()
    {
        $queries = [
            ['id', 'IN', [1234, 5678]],
            ['other', '>', 1234]
        ];

        $expected = [
            [
                'attribute' => 'id',
                'operator' => 'IN',
                'value' => [[1234, 5678]],
                'type' => 'and'
            ],
            [
                'attribute' => 'other',
                'operator' => '>',
                'value' => 1234,
                'type' => 'and'
            ]
        ];

        $results = $this->parser->parseAndQuery($queries);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the return of a parsed `AND` query
     *
     * @return void
     */
    public function testParseOrQuery()
    {
        $queries = [
            ['id', 'IN', [1234, 5678]],
            ['other', '>', 1234]
        ];

        $expected = [
            [
                'attribute' => 'id',
                'operator' => 'IN',
                'value' => [[1234, 5678]],
                'type' => 'or'
            ],
            [
                'attribute' => 'other',
                'operator' => '>',
                'value' => 1234,
                'type' => 'or'
            ]
        ];

        $results = $this->parser->parseOrQuery($queries);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the return of a parsed `Nested` query
     *
     * @return void
     */
    public function testParseNestedQuery()
    {
        $queries = [
            'and' => [
                'and' => [['last_name', 'smith']],
                'or' => [['first_name', 'bob']]
            ],
            'or' => [
                'and' => [['last_name', 'smith']]
            ]
        ];

        $expected = [
            [
                'attribute' => 'and',
                'operator' => null,
                'value' => [
                    'and' => [
                        [
                            'attribute' => 'last_name',
                            'operator' => '=',
                            'value' => 'smith',
                            'type' => 'and'
                        ]
                    ],
                    'or' => [
                        [
                            'attribute' => 'first_name',
                            'operator' => '=',
                            'value' => 'bob',
                            'type' => 'or'
                        ]
                    ]
                ],
                'type' => 'nested'
            ],
            [
                'attribute' => 'or',
                'operator' => null,
                'value' => [
                    'and' => [
                        [
                            'attribute' => 'last_name',
                            'operator' => '=',
                            'value' => 'smith',
                            'type' => 'and'
                        ]
                    ]
                ],
                'type' => 'nested'
            ]
        ];

        $results = $this->parser->parseNestedQuery($queries);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the return of a parsed `Nested` query
     *
     * @return void
     */
    public function testParseHasQuery()
    {
        $queries = [
            'and' => [
                'and' => [['last_name', 'smith']],
                'or' => [['first_name', 'bob']]
            ],
            'or' => [
                'and' => [['last_name', 'smith']]
            ]
        ];

        $expected = [
            [
                'attribute' => 'and',
                'operator' => null,
                'value' => [
                    'and' => [
                        [
                            'attribute' => 'last_name',
                            'operator' => '=',
                            'value' => 'smith',
                            'type' => 'and'
                        ]
                    ],
                    'or' => [
                        [
                            'attribute' => 'first_name',
                            'operator' => '=',
                            'value' => 'bob',
                            'type' => 'or'
                        ]
                    ]
                ],
                'type' => 'has'
            ],
            [
                'attribute' => 'or',
                'operator' => null,
                'value' => [
                    'and' => [
                        [
                            'attribute' => 'last_name',
                            'operator' => '=',
                            'value' => 'smith',
                            'type' => 'and'
                        ]
                    ]
                ],
                'type' => 'has'
            ]
        ];

        $results = $this->parser->parseHasQuery($queries);

        $this->assertEquals($expected, $results);
    }
}

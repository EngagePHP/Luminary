<?php

use Luminary\Services\ApiQuery\Filters\Composer;

class QueryFilterComposerTest extends TestCase
{
    /**
     * Test that the parse method returns
     * the correct array
     *
     * @return void
     */
    public function testComposerAndFormat()
    {
        $queries = [
            ['first_name','bob'],
            ['title', ['mr', 'Mr', 'MR']],
            ['id', 'IN', [1234, 5678]],
            ['other', '>', 1234]
        ];

        $expected = [
            [
                'attribute' => 'first_name',
                'operator' => '=',
                'value' => 'bob',
                'type' => 'and'
            ],
            [
                'attribute' => 'title',
                'operator' => 'IN',
                'value' => [['mr', 'Mr', 'MR']],
                'type' => 'and'
            ],
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

        $results = [];

        foreach($queries as $query) {
            $results[] = Composer::format('and', $query);
        }

        $this->assertEquals($expected, $results);
    }

    /**
     * Test that the parse method returns
     * the correct array
     *
     * @return void
     */
    public function testComposerOrFormat()
    {
        $queries = [
            ['first_name','bob'],
            ['title', ['mr', 'Mr', 'MR']],
            ['id', 'IN', [1234, 5678]],
            ['other', '>', 1234]
        ];

        $expected = [
            [
                'attribute' => 'first_name',
                'operator' => '=',
                'value' => 'bob',
                'type' => 'or'
            ],
            [
                'attribute' => 'title',
                'operator' => 'IN',
                'value' => [['mr', 'Mr', 'MR']],
                'type' => 'or'
            ],
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

        $results = [];

        foreach($queries as $query) {
            $results[] = Composer::format('or', $query);
        }

        $this->assertEquals($expected, $results);
    }

    /**
     * Test that the nested format method returns
     * the correct array
     *
     * @return void
     */
    public function testComposerNestedFormat()
    {
        $queries = [
            'and' => [],
            'or' => []
        ];

        $expected = [
            [
                'attribute' => 'and',
                'operator' => null,
                'value' => [],
                'type' => 'nested'
            ],
            [
                'attribute' => 'or',
                'operator' => null,
                'value' => [],
                'type' => 'nested'
            ]
        ];

        $results = [];

        foreach($queries as $type => $query) {
            $results[] = Composer::formatNested($type, $query);
        }

        $this->assertEquals($expected, $results);
    }

    /**
     * Test that the nested format method returns
     * the correct array
     *
     * @return void
     */
    public function testComposerHasFormat()
    {
        $queries = [
            'and' => [],
            'or' => []
        ];

        $expected = [
            [
                'attribute' => 'and',
                'operator' => null,
                'value' => [],
                'type' => 'has'
            ],
            [
                'attribute' => 'or',
                'operator' => null,
                'value' => [],
                'type' => 'has'
            ]
        ];

        $results = [];

        foreach($queries as $type => $query) {
            $results[] = Composer::formatHas($type, $query);
        }

        $this->assertEquals($expected, $results);
    }
}

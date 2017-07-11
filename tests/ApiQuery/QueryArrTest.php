<?php

use Luminary\Services\ApiQuery\QueryArr;

class QueryArrTest extends TestCase
{
    /**
     * The is nested method should return true
     * if a nested array is an associated array
     * and false if indexed
     *
     * @return void
     */
    public function testIsNestedArrayShouldReturnBoolean()
    {
        $associative = QueryArr::isNested([
            'hello' => ['one','two','three'],
            'hola' => 'hello'
        ]);

        $indexed = QueryArr::isNested([
            'hello',
            'hola'
        ]);

        $this->assertTrue($associative);
        $this->assertNotTrue($indexed);
    }

    /**
     * The dotValue method should return
     * an indexed array of dots as a
     * multidimensional array with the last
     * dot as a value and any non dot valued
     * within a default array
     *
     * @return void
     */
    public function testDotValue()
    {
        $array = [
            'single',
            'singleTwo',
            'parent.email',
            'parent.test',
            'parent.sub.one',
            'parent.sub.two',
            'parent.subTwo.one'
        ];

        $resultOne = [
            'default' => [
                'single',
                'singleTwo'
            ],
            'parent' => [
                'email',
                'test',
                'sub' => [
                    'one',
                    'two'
                ],
                'subTwo' => 'one'
            ]
        ];

        $resultTwo = [
            'test' => [
                'single',
                'singleTwo'
            ],
            'parent' => [
                'email',
                'test',
                'sub' => [
                    'one',
                    'two'
                ],
                'subTwo' => 'one'
            ]
        ];

        $qarrOne = QueryArr::dotValue($array);
        $qarrTwo = QueryArr::dotValue($array, 'test');

        $this->assertEquals($resultOne, $qarrOne);
        $this->assertEquals($resultTwo, $qarrTwo);
    }

    /**
     * The dotReverse method should return
     * a multidimensional array hydrated back
     * out from the array_dot laravel helper
     *
     * @return void
     */
    public function testDotReverse()
    {
        $array = [
            'single' => 'single value',
            'singleTwo' => 'Single Two Value',
            'parent.email' => 'Parent Email Value',
            'parent.test' => 'Parent Test Value',
            'parent.sub.one' => 'Parent Sub One Value',
            'parent.sub.two' => 'Parent Sub Two Value',
            'parent.subTwo.one' => 'Parent SubTwo One Value'
        ];

        $result = [
            'single' => 'single value',
            'singleTwo' => 'Single Two Value',
            'parent' => [
                'email' => 'Parent Email Value',
                'test' => 'Parent Test Value',
                'sub' => [
                    'one' => 'Parent Sub One Value',
                    'two' => 'Parent Sub Two Value'
                ],
                'subTwo' => [
                    'one' => 'Parent SubTwo One Value'
                ]
            ]
        ];

        $qarr = QueryArr::dotReverse($array);
        $this->assertEquals($result, $qarr);
    }
}

<?php

use Luminary\Services\ApiQuery\QueryParser;

class QueryParserTest extends TestCase
{
    /**
     * Check that double quotes get removed
     * from strings
     *
     * @return void
     */
    public function testShouldRemoveDoubleQuotesFromString()
    {
        $string = QueryParser::stripQuotes('string"');

        $this->assertEquals('string', $string);
    }

    /**
     * Check that dashes, slashes and spaces get
     * converted to underscores
     *
     * @return void
     */
    public function testShouldReplaceCharacterStringsWithUnderscores()
    {
        $replaced = QueryParser::replaceWithUnderscores('im/a.-string with-dashes/slashes and spaces');

        $this->assertEquals('im_a._string_with_dashes_slashes_and_spaces', $replaced);
    }

    /**
     * Clean multidimensional array keys by removing double
     * quotes and replacing characters with underscores
     *
     * @return void
     */
    public function testShouldCleanMultidimensionalArrayKeys()
    {
        $sanitized = QueryParser::sanitize([
            '"double"' => [
                '"double child"' => ''
            ],
            'im-a string/with.underscores' => ''
        ]);
        $match = [
            'double' => [
                'double_child' => ''
            ],
            'im_a_string_with.underscores' => ''
        ];

        $this->assertEquals($match, $sanitized);
    }

    /**
     * Test the splitList method should parse a comma
     * separated list into an array
     *
     * @return void
     */
    public function testShouldParsesACommaSeparatedListIntoAnArray()
    {
        $parsed = QueryParser::splitList('one, two, three');
        $match = ['one','two','three'];

        $this->assertEquals($match, $parsed);
    }

    /**
     * Test that the expand method loops through a multidimensional array
     * and converts comma separated lists into arrays
     *
     * @return void
     */
    function testShouldLoopThroughAMultidimensionalArrayAndConvertCommaSeparatedListsToArrays()
    {
        $expanded = QueryParser::expand([
            'simple' => '1, 2,3,5, 8',
            'complex' =>[
                '5,4,6,8',
                '4,1,5,4'
            ],
            'ignore' => 'I\'m just a string'
        ]);
        $match = [
            'simple' => ['1','2','3','5','8'],
            'complex' =>[
                ['5','4','6','8'],
                ['4','1','5','4']
            ],
            'ignore' => 'I\'m just a string'
        ];

        $this->assertEquals($match, $expanded);
    }
}

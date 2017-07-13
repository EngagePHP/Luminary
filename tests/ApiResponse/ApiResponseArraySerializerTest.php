<?php

use Luminary\Services\ApiResponse\Serializers\ArraySerializer;

class ApiResponseArraySerializerTest extends TestCase
{
    /**
     * Test the id method
     *
     * @return void
     */
    public function testArraySerializer()
    {
        $items = ['one', 'two', 'three'];
        $expected = [
            'jsonapi' => [
                'version' => '1.0'
            ],
            'links' => [
                'self' => 'http://localhost'
            ],
            'data' => $items,
            'included' => [],
            'meta' => []
        ];

        $s = new ArraySerializer($items);

        $this->assertEquals($expected, $s->serialize());
    }
}

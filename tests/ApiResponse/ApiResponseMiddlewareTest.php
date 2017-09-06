<?php

use Luminary\Services\Testing\Models\Customer;

class ApiResponseMiddlewareTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(10, 2, 2);
        $this->setUpQuery();
    }

    /**
     * Test the id method
     *
     * @return void
     */
    public function testCollectionSerializerResponse()
    {
        $this->get('customers?include=location&fields[location]=name');

        $this->seeJsonStructure([
            'jsonapi' => [
                'version'
            ],
            'links' => [
                'self'
            ],
            'data' => [],
            'included' => [],
            'meta' => [
                'response_time'
            ]
        ]);
    }

    /**
     * Test the id method
     *
     * @return void
     */
    public function testArraySerializerResponse()
    {
        app()->router->get('array', function() {
            return [];
        });

        $this->get('array?include=location&fields[location]=name');

        $this->seeJsonStructure([
            'jsonapi' => [
                'version'
            ],
            'links' => [
                'self'
            ],
            'data' => [],
            'included' => [],
            'meta' => [
                'response_time'
            ]
        ]);
    }

    /**
     * Test the id method
     *
     * @return void
     */
    public function testModelSerializerResponse()
    {
        $id = $this->customers->first()->id;
        $this->get('customers/'.$id.'?include=location&fields[location]=name');

        $this->seeJsonStructure([
            'jsonapi' => [
                'version'
            ],
            'links' => [
                'self'
            ],
            'data' => [
                'type',
                'id',
                'attributes',
                'links',
                'relationships'
            ],
            'included' => [],
            'meta' => [
                'response_time'
            ]
        ]);
    }
}

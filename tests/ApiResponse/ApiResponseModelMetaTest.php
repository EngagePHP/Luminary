<?php

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiResponse\ResponseHelper;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;

class ApiResponseModelMetaTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * The model instance
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The Serializer instance
     *
     * @var \Luminary\Services\ApiResponse\Serializers\ModelSerializer
     */
    protected $serializer;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->seed(2, 2, 2);

        TenantModelScope::setOverride();
    }

    /**
     * Test the id method
     *
     * @return void
     */
    public function testMetaMethod()
    {
        $model = Customer::all()->first()->setMetaKeys(['created_at', 'updated_at']);
        $expected = [
            'created_at' => $model->getAttributeValue('created_at'),
            'updated_at' => $model->getAttributeValue('updated_at')
        ];

        $this->assertEquals($expected, $model->meta());
    }
}

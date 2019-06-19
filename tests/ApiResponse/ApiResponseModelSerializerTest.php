<?php

use Illuminate\Database\Eloquent\Model;
use Luminary\Services\ApiResponse\ResponseHelper;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;
use Luminary\Services\Tenants\TenantModelScope;
use Luminary\Services\Testing\Models\Customer;

class ApiResponseModelSerializerTest extends TestCase
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
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(2, 2, 2);
        TenantModelScope::setOverride();
        $this->model = Customer::with('location', 'users', 'users.location')->first();
    }

    /**
     * Test the id method
     *
     * @return void
     */
    public function testIdMethods()
    {
        $s = $this->newSerializer();
        $id = 1234;

        $s->setId($id);

        $this->assertEquals((string)$id, $s->id());
    }

    /**
     * Test the attributes methods
     *
     * @return void
     */
    public function testAttributesMethods()
    {
        $s = $this->newSerializer();
        $attributes = [
            'id' => 1234,
            'type' => 'anything',
            'name' => 'SuperCorp',
            'website' => 'http://www.supercorp.com',
            'phone' => '444-555-4444'
        ];
        $expected = array_only($attributes, ['name', 'website', 'phone']);

        $s->setAttributes($attributes);

        $this->assertEquals($expected, $s->attributes());
    }

    /**
     * Test the type method
     *
     * @return void
     */
    public function testTypeMethods()
    {
        $s = $this->newSerializer();
        $expected = 'customers';

        $s->setType($expected);

        $this->assertEquals($expected, $s->type());
    }

    /**
     * Test that included method returns
     * as an empty array if not set
     *
     * @return void
     */
    public function testEmptyIncludedMethod()
    {
        $this->assertEmpty(($this->newSerializer())->included());
    }

    /**
     * Test that relashionships method returns
     * as an empty array if not set
     *
     * @return void
     */
    public function testEmptyRelationshipMethod()
    {
        $this->assertEmpty(($this->newSerializer())->included());
    }

    /**
     * Test the array return of the data method
     *
     * @return void
     */
    public function testDataMethod()
    {
        $s = $this->newSerializer();
        $now = (string) Carbon\Carbon::now();
        $attributes = [
            'id' => 1234,
            'type' => 'anything',
            'name' => 'SuperCorp',
            'website' => 'http://www.supercorp.com',
            'phone' => '444-555-4444'
        ];

        // Set the serializer attributes
        $s->setType('customers')->setId(1234)->setAttributes($attributes)->setMeta(['created_at' => $now, 'updated_at' => $now, 'deleted_at' => null]);

        // What we should expect back
        $expected = [
            'type' => 'customers',
            'id' => '1234',
            'attributes' => array_only($attributes, ['name', 'website', 'phone']),
            'links' => [
                'self' => ResponseHelper::resourceSelf(1234, 'customers')
            ],
            'relationships' => [],
            'meta' => [
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null
            ]
        ];

        $this->assertEquals($expected, $s->data());
    }

    /**
     * Test the set/get meta methods
     *
     * @return void
     */
    public function testMetaMethods()
    {
        $s = $this->newSerializer();
        $now = (string) Carbon\Carbon::now();

        $s->setMeta(['created_at' => $now, 'updated_at' => $now, 'deleted_at' => null]);

        $this->assertEquals([
            'created_at' => $now,
            'updated_at' => $now,
            'deleted_at' => null
        ], $s->meta());
    }

    /**
     * Test the setup of the relations method
     *
     * @return void
     */
    public function testRelationsMethod()
    {
        $m = $this->model;
        $s = $this->newSerializer();

        $s->setRelations($m->getRelations())
            ->setRelationships($s->relations())
            ->setIncluded($s->flattenedRelations());

        $this->assertCount(3, $s->included());
        $this->assertEquals(['location', 'users'], array_keys($s->relationships()));
    }

    /**
     * Test that the method returns a
     * collection of serialized models
     *
     * @return void
     */
    public function testSerializeModelsMethod()
    {
        $models = $this->locations->take(3);
        $models = new \Illuminate\Database\Eloquent\Collection($models->all());

        $s = $this->newSerializer();

        $s->serializeModels($models)->each(
            function($model) {
                $this->assertInstanceOf(ModelSerializer::class, $model);
            }
        );
    }

    /**
     * Create a new CollectionSerializer Instance
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $attributes
     * @return ModelSerializer
     */
    protected function newSerializer(Model $model = null, $attributes = [])
    {
        $model = $model ?: new Customer($attributes);
        return new ModelSerializer($model);
    }
}

<?php

use Illuminate\Support\Collection;
use Luminary\Services\ApiResponse\Serializers\CollectionSerializer;
use Luminary\Services\Testing\Models\Customer;

class ApiResponseCollectionSerializerTest extends TestCase
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

        $this->seed(10, 2, 2);
        $this->setUpQuery();
    }

    /**
     * Test the set/get collection methods
     *
     * @return void
     */
    public function testCollectionMethods()
    {
        $data = [1,2,3,4];

        $results = $this->newSerializer()->setCollection(collect($data), false)->collection()->all();

        $this->assertEquals($data, $results);
    }

    /**
     * Test the set/get data methods
     *
     * @return void
     */
    public function testDataMethods()
    {
        $data = [1,2,3,4];

        $results = $this->newSerializer()->setData($data)->data();

        $this->assertEquals($data, $results);
    }

    /**
     * Test the set/get included methods
     *
     * @return void
     */
    public function testIncludedMethods()
    {
        $includeOne = [1,2,3,4];
        $includeTwo = [4,5,6,7];
        $includeThree = [7,8,9];

        $expected = [1,2,3,4,5,6,7,8,9];

        $results = $this->newSerializer()
            ->setIncluded($includeOne)
            ->addIncluded($includeTwo)
            ->addIncluded($includeThree)
            ->included();

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the jsonapi version method
     *
     * @return void
     */
    public function testJsonapiMethod()
    {
        $results = $this->newSerializer()->jsonapi();

        $this->assertRegExp('/^(?:(\d+)\.)?(?:(\d+)\.)?(\*|\d+)$/', $results);
    }

    /**
     * Test the links method
     *
     * @return void
     */
    public function testLinksMethod()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users',
            'page' => [
                'number' => 2,
                'size' => 3
            ]
        ];

        $this->query->setQuery($query)->activate();
        $collection = Customer::all();

        $results = $this->newSerializer()
            ->setResource('customers')
            ->setCollection($collection, false)
            ->links();

        $expected = [
            'self' => "http://localhost/customers",
            'first' => "http://localhost/customers/?include=users&page%5Bnumber%5D=1&page%5Bsize%5D=3",
            'last' => "http://localhost/customers/?include=users&page%5Bnumber%5D=4&page%5Bsize%5D=3",
            'prev' => "http://localhost/customers/?include=users&page%5Bnumber%5D=1&page%5Bsize%5D=3",
            'next' => "http://localhost/customers/?include=users&page%5Bnumber%5D=3&page%5Bsize%5D=3"
        ];

        $this->assertEquals($expected, $results);

        Customer::clearBootedModels();
    }

    /**
     * Test the links method
     *
     * @return void
     */
    public function testSerializeMethod()
    {
        $query = [
            'resource' => 'customers',
            'include' => 'users',
            'paginate' => [
                'number' => 2,
                'size' => 3
            ]
        ];

        $this->query->setQuery($query)->activate();
        $collection = Customer::all();

        $serializer = $this->newSerializer($collection, 'customers');

        $results = $serializer->serialize();

        $expected = [
            'jsonapi' => [
                'version' => $serializer->jsonapi()
            ],
            'links' => $serializer->links(),
            'data' => $serializer->data(),
            'included' => $serializer->included(),
            'meta' => $serializer->meta()
        ];

        $this->assertEquals($expected, $results);

        Customer::clearBootedModels();
    }

    /**
     * Create a new CollectionSerializer Instance
     *
     * @param Collection|null $collection
     * @param string $resource
     * @return CollectionSerializer
     */
    protected function newSerializer(Collection $collection = null, string $resource = null)
    {
        $collection = $collection ?: collect();
        return new CollectionSerializer($collection, $resource);
    }
}

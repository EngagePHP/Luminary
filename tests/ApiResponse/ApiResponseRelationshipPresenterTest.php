<?php

use Luminary\Services\ApiResponse\Presenters\RelationshipPresenter;
use Luminary\Services\ApiResponse\ResponseHelper;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;
use Luminary\Services\Testing\Models\Customer;

class ApiResponseRelationshipPresenterTest extends TestCase
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

        $this->seed(2, 2, 2);
    }

    /**
     * Test formatting a single model
     *
     * @return void
     */
    public function testFormatModel()
    {
        $model = $this->locations->first();
        $model = new ModelSerializer($model);

        $expected = [
            'id' => $model->id(),
            'type' => $model->type()
        ];
        $results = (new RelationshipPresenter(new ModelSerializer(new Customer), collect()))->formatModel($model);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test formatting of multiple models
     *
     * @return void
     */
    public function testFormatModels()
    {
        $models = $this->locations->map(
            function($model) {
                return new ModelSerializer($model);
            }
        );
        $expected = $models->map(
            function($model) {
                return [
                    'id' => $model->id(),
                    'type' => $model->type()
                ];
            }
        )->all();

        $results = (new RelationshipPresenter(new ModelSerializer(new Customer), collect()))->formatModels($models);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the format method with an empty collection
     *
     * @return void
     */
    public function testFormatWithEmptyCollection()
    {
        $customer = $this->customers->first();
        $serializer = new ModelSerializer($customer);
        $models = collect(['locations' => collect()]);
        $expected = [
            'locations' =>
                [
                    'links' => ResponseHelper::generateRelationshipLinks($serializer->type(), $serializer->id(), 'locations', true),
                    'data' => []
                ]
        ];
        $results = (new RelationshipPresenter($serializer, $models))->format();

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the format method with a collection of models
     *
     * @return void
     */
    public function testFormatMethod()
    {
        $customer = $this->customers->first();
        $serializer = new ModelSerializer($customer);
        $models = $customer->users->map(
            function($model) {
                return new ModelSerializer($model);
            }
        );
        $collection = collect(['users' => $models ]);
        $expected = [
            'users' => [
                'links' => ResponseHelper::generateRelationshipLinks($serializer->type(), $serializer->id(), 'users', true),
                'data' => $models->map(function($model) {
                    return [
                        'id' => $model->id(),
                        'type' => $model->type()
                    ];
                })->all()
            ]
        ];
        $results = (new RelationshipPresenter($serializer, $collection))->format();

        $this->assertEquals($expected, $results);
    }
}

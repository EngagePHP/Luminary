<?php

use Luminary\Services\ApiResponse\Presenters\IncludedPresenter;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;

class ApiResponseIncludedPresenterTest extends TestCase
{
    use Luminary\Services\Testing\BaseTestingTrait;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
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
            'type' => $model->type(),
            'attributes' => $model->attributes(),
            'links' => $model->links(),
            'relationships' => $model->relationships(),
            'meta' => $model->meta()
        ];

        $results = (new IncludedPresenter([]))->formatModel($model);

        $this->assertEquals($expected, $results);
    }

    /**
     * Test the format method with a collection of models
     *
     * @return void
     */
    public function testFormatMethod()
    {
        $models = $this->users->map(
            function($model) {
                return new ModelSerializer($model);
            }
        );

        $expected = $models->map(
            function(ModelSerializer $model) {
                return [
                    'id' => $model->id(),
                    'type' => $model->type(),
                    'attributes' => $model->attributes(),
                    'links' => $model->links(),
                    'relationships' => $model->relationships(),
                    'meta' => $model->meta()
                ];
            }
        )->all();

        $results = (new IncludedPresenter($models->all()))->format();

        $this->assertEquals($expected, $results);
    }
}

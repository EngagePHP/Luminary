<?php

namespace Luminary\Services\ApiQuery\Testing;

use Luminary\Services\ApiQuery\Query;

trait BaseQueryTrait
{
    /**
     * The fully generated url
     *
     * @var string
     */
    protected $url;

    /**
     * The query instance
     *
     * @var \Luminary\Services\ApiQuery\Query
     */
    protected $query;

    /**
     * Get the query instance as an array
     *
     * @return array
     */
    protected function getQueryArray()
    {
        return $this->query->toArray();
    }

    /**
     * Setup the routes for running middleware tests
     *
     * @return void
     */
    protected function setUpRoutes()
    {
        $app = app();

        $app->get('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->post('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->put('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->patch('api-query-middleware', function () {
            return response('api query middleware', 200);
        });

        $app->delete('api-query-middleware', function () {
            return response('api query middleware', 200);
        });
    }

    /**
     * Create the HTTP url string w/parameters for testing
     *
     * @return void
     */
    protected function setUpUrl()
    {
        $this->url = '/api-query-middleware?' . http_build_query($this->queryString);
    }

    /**
     * Setup the query instance for testing
     *
     * @return void
     */
    protected function setUpQuery()
    {
        $this->query = app(Query::class)->activate();
    }
}

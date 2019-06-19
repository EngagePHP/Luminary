<?php

use Illuminate\Http\Request;

class ApiRequestResourceTest extends TestCase
{
    /**
     * Required request headers
     *
     * @var array
     */
    protected $headers = ['CONTENT_TYPE' => 'application/vnd.api+json'];

    /**
     * Test data attribute failure
     *
     * @return void
     */
    public function testTypeAndResource() :void
    {
        $phpunit = $this;
        $router = app()->router;

        $router->group(['middleware' => ['request.headers', 'request.middleware']], function($router) use($phpunit) {
            $router->get('multi-word-resource', function(Request $request) use($phpunit) {
                $phpunit->assertEquals('multi_word_resource', $request->type());
                $phpunit->assertEquals('multi-word-resource', $request->resource());
            });
        });

        $this->get('multi-word-resource', $this->headers);
    }
}

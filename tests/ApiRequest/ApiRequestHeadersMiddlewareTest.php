<?php

use Illuminate\Http\Request;
use Luminary\Services\ApiRequest\Exceptions\MediaTypeParametersNotAllowed;
use Luminary\Services\ApiRequest\Exceptions\UnsupportedMediaType;
use Luminary\Services\ApiRequest\Middleware\RequestHeaders;

class ApiRequestHeadersMiddlewareTest extends TestCase
{
    /**
     * The JsonApiRequest instance
     *
     * @var \Luminary\Services\ApiRequest\Middleware\RequestHeaders
     */
    protected $middleware;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->middleware = new RequestHeaders;
        $this->app->instance('middleware.disable', true);
    }

    /**
     * Check that header matches exactly
     *
     * @return void
     */
    public function testIsStrictMethod() :void
    {
        $middleware = $this->middleware;

        $this->assertTrue($middleware->isStrictMatch('application/vnd.api+json'));
        $this->assertFalse($middleware->isStrictMatch('application/vnd.api'));
    }

    /**
     * Check that the vendor tree and producer
     * supplied is correct
     *
     * @return void
     */
    public function testCheckVendorMethod() :void
    {
        $middleware = $this->middleware;

        $this->assertTrue($middleware->isCorrectVendor('application/vnd.api+json'));
        $this->assertFalse($middleware->isCorrectVendor('application/vnd.test'));
    }

    /**
     * Check that the media type supplied is correct
     *
     * @return void
     */
    public function testCheckMediaTypeMethod() :void
    {
        $middleware = $this->middleware;

        $this->assertTrue($middleware->hasCorrectMediaType('application/vnd.api+json'));
        $this->assertFalse($middleware->hasCorrectMediaType('application/vnd.api+xml'));
    }

    /**
     * Check that a api header does not include
     * any additional parameters
     *
     * @return void
     */
    public function testCheckParameters() :void
    {
        $middleware = $this->middleware;

        $this->assertFalse($middleware->hasAdditionalParameters('application/vnd.api+json;'));
        $this->assertFalse($middleware->hasAdditionalParameters('application/vnd.vendor+xml; charset=utf-8'));
        $this->assertTrue($middleware->hasAdditionalParameters('application/vnd.api+json; charset=utf-8'));
    }

    /**
     * Test that strict checking for content type passes
     *
     * @return void
     */
    public function testStrictContentTypePasses()
    {
        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();

        // Create the request
        $request = Request::create('http://example.com/api', 'GET');
        $request->headers->set('content-type', 'application/vnd.api+json');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );

        $this->assertSame('success', $response->getContent());
    }

    /**
     * Test that the media type check fails
     * for content-type check
     *
     * @return void
     */
    public function testContentTypeIncorrectMediaTypeFails()
    {
        $this->expectException(UnsupportedMediaType::class);
        $this->expectExceptionCode(415);

        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();
        $response->getContent();

        // Create the request
        $request = Request::create('http://example.com/api', 'POST');
        $request->headers->set('content-type', 'application/wrong.api+json');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );
    }

    /**
     * Test that the additional media type parameters
     * check fails for content-type check
     *
     * @return void
     */
    public function testContentTypeMediaTypeParametersFail()
    {
        $this->expectException(MediaTypeParametersNotAllowed::class);
        $this->expectExceptionCode(406);

        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();
        $response->getContent();

        // Create the request
        $request = Request::create('http://example.com/api', 'POST');
        $request->headers->set('content-type', 'application/vnd.api+json; charset=utf-8');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );
    }

    /**
     * Test that strict checking for accept headers pass
     *
     * @return void
     */
    public function testStrictAcceptPasses()
    {
        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();

        // Create the request
        $request = Request::create('http://example.com/api', 'GET');
        $request->headers->set('accept', 'application/vnd.api+json');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );

        $this->assertSame('success', $response->getContent());
    }

    /**
     * Test that the media type check fails
     * for accept headers
     *
     * @return void
     */
    public function testAcceptIncorrectMediaTypeFails()
    {
        $this->expectException(UnsupportedMediaType::class);
        $this->expectExceptionCode(415);

        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();
        $response->getContent();

        // Create the request
        $request = Request::create('http://example.com/api', 'POST');
        $request->headers->set('content-type', 'application/api.api+json');
        $request->headers->set('accept', 'application/vnd.wrong+json');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );
    }

    /**
     * Test that the additional media type parameters
     * check fails for accept headers
     *
     * @return void
     */
    public function testAcceptMediaTypeParametersFail()
    {
        $this->expectException(MediaTypeParametersNotAllowed::class);
        $this->expectExceptionCode(406);

        // Create the response
        $response = Mockery::mock('Illuminate\Http\Response')->shouldReceive('getContent')->once()->andReturn('success')->getMock();
        $response->getContent();

        // Create the request
        $request = Request::create('http://example.com/api', 'GET');
        $request->headers->set('content-type', 'application/vnd.api+json');
        $request->headers->set('accept', 'application/vnd.api+json; charset=utf-8');

        // Pass it to the middleware
        $this->middleware->handle($request,
            function () use ($response) {
                return $response;
            }
        );
    }
}

<?php

use Illuminate\Http\Request;
use Luminary\Exceptions\Handler;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExceptionResponseHandlerTest extends TestCase
{
    /**
     * Check that the application exception handler
     * returns the correct response
     *
     * @return void
     */
    public function testServerError() :void
    {
        $request = Request::create('http://example.com/api', 'GET');
        $handler = new Handler;
        $exception = new HttpException(500, 'An internal server error has occurred');
        $expected = [
            'errors' => [
                [
                    'status' => 500,
                    'title' => 'An unknown error has occurred',
                    'detail' => 'An internal server error has occurred'
                ]
            ]
        ];

        $this->response = app()->prepareResponse(
            $handler->render($request, $exception)
        );

        $this->seeJson($expected);
    }
}

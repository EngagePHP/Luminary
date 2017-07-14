<?php

use Luminary\Exceptions\Presenters\HttpExceptionPresenter;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpExceptionPresenterTest extends TestCase
{
    /**
     * Check that the http exception presenter
     * returns the correct data
     *
     * @return void
     */
    public function testHttpExceptionPresenter() :void
    {
        $e = new HttpException(500);
        $response = (new HttpExceptionPresenter($e))->response();
        $expected = [[
            'status' => 500,
            'title' => 'An internal server error has occurred',
            'detail' => ''
        ]];

        $this->assertEquals($expected, $response);
    }
}

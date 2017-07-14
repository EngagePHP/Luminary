<?php

use Luminary\Exceptions\Presenters\DefaultPresenter;

class ExceptionDefaultPresenterTest extends TestCase
{
    /**
     * Check that the default presenter returns
     * correct data
     *
     * @return void
     */
    public function testDefaultPresenter() :void
    {
        $e = new Exception('this is a default exception', 404);
        $response = (new DefaultPresenter($e))->response();
        $expected = [[
            'status' => 404,
            'title' => 'An unknown error has occurred',
            'detail' => 'this is a default exception'
        ]];

        $this->assertEquals($expected, $response);
    }
}

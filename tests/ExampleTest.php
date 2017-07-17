<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $this->get('/', ['content-type' => 'application/vnd.api+json']);
        $content = json_decode($this->response->getContent(), true);
        $data = array_get($content, 'data');

        $this->assertEquals(
            [$this->app->version()], $data
        );
    }
}

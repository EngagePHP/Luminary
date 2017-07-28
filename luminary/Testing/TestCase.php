<?php

namespace Luminary\Testing;

use Illuminate\Support\Facades\Artisan;
use TestCase as LumenTestCase;

class TestCase extends LumenTestCase
{
    /**
     * Run a specific database seeder
     *
     * @param null $class
     * @return \Laravel\Lumen\Application
     */
    public function seed($class = null)
    {
        $options = is_null($class) ? [] : ['--class' => $class];

        Artisan::call('db:seed', $options);
    }
}

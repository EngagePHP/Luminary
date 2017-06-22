<?php

namespace Luminary\Services\ApiLoader\Database;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * The loaded seeders array
     *
     * @var array
     */
    private $seeders;

    /**
     * DatabaseSeeder constructor.
     *
     * @param array $seeders
     */
    public function __construct(array $seeders)
    {
        $this->seeders = $seeders;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->seeders as $seeder) {
            $this->call($seeder);
        }
    }
}

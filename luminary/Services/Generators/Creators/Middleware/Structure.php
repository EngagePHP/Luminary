<?php

namespace Luminary\Services\Generators\Creators\Middleware;

use Luminary\Services\Generators\Contracts\StructureInterface;
use Luminary\Services\Filesystem\App\Storage;

class Structure implements StructureInterface
{
    /**
     * Scaffold a new directory structure
     *
     * @param $path
     * @return mixed
     */
    public static function create($path)
    {
        Storage::makeDirectory($path.'/Middleware', true);
    }
}

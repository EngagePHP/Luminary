<?php

namespace Luminary\Services\Generators\Contracts;

interface StructureInterface
{
    /**
     * Scaffold a new directory structure
     *
     * @param $path
     * @return mixed
     */
    public static function create($path);
}

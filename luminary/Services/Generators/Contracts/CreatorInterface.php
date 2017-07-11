<?php

namespace Luminary\Services\Generators\Contracts;

interface CreatorInterface
{
    /**
     * Create a file
     *
     * @param string $name
     * @param string $path
     * @return mixed
     */
    public static function create(string $name, string $path);
}

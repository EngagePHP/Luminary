<?php

namespace Luminary\Services\ApiLoader\Registry;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

class Registry
{
    /**
     * The registered commands
     *
     * @var \Illuminate\Support\Collection
     */
    protected $commands;

    /**
     * The registered configs
     *
     * @var \Illuminate\Support\Collection
     */
    protected $configs;

    /**
     * A list of console kernel classes
     *
     * @var \Illuminate\Support\Collection
     */
    protected $consoleKernels;

    /**
     * A list of model factories
     *
     * @var \Illuminate\Support\Collection
     */
    protected $modelFactories;

    /**
     * The registered middleware
     *
     * @var \Illuminate\Support\Collection
     */
    protected $middleware;

    /**
     * The registered migrations
     *
     * @var \Illuminate\Support\Collection
     */
    protected $migrations;

    /**
     * The registered providers
     *
     * @var \Illuminate\Support\Collection
     */
    protected $providers;

    /**
     * The registered routes
     *
     * @var \Illuminate\Support\Collection
     */
    protected $routes;

    /**
     * The registered route middleware
     *
     * @var \Illuminate\Support\Collection
     */
    protected $routeMiddleware;

    /**
     * The registered database seeders
     *
     * @var \Illuminate\Support\Collection
     */
    protected $seeders;

    /**
     * The registered views
     *
     * @var \Illuminate\Support\Collection
     */
    protected $views;

    /**
     * Replace all properties with an
     * array of properties
     *
     * @param array $properties
     */
    public function fill(array $properties)
    {
        $keys = $this->properties()->keys();

        foreach ($keys as $property) {
            $values = array_get($properties, $property, []);
            $this->{$property} = collect($values);
        }
    }

    /**
     * Get a property as an array
     *
     * @param $property
     * @return array
     */
    public function get($property) :array
    {
        return $this->property($property)->toArray();
    }

    /**
     * Return the registry as an array
     *
     * @return array
     */
    public function toArray() :array
    {
        return $this->properties()->toArray();
    }

    /**
     * Get a property as an array
     *
     * @param $property
     * @return \Illuminate\Support\Collection
     */
    public function __get($property) :Collection
    {
        return $this->property($property);
    }

    /**
     * Set/merge a properties values
     *
     * @param $property
     * @param array $values
     */
    public function __set($property, array $values)
    {
        $this->{$property} = $this->property($property)->merge($values);
    }

    /**
     * Get the properties list
     *
     * @return \Illuminate\Support\Collection
     */
    private function properties() :Collection
    {
        $properties = (new ReflectionClass($this))->getProperties(ReflectionProperty::IS_PROTECTED);

        return collect($properties)
            ->map(
                function (ReflectionProperty $property) {
                    return $property->getName();
                }
            )
            ->flip()
            ->map(
                function ($item, $key) {
                    return $this->property($key);
                }
            );
    }

    /**
     * Return the property or an empty Collection
     *
     * @param string $name
     * @return \Illuminate\Support\Collection
     */
    private function property(string $name) :Collection
    {
        return $this->{$name} ?: collect();
    }
}

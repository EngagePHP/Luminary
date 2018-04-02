<?php

namespace Luminary\Services\ApiLoader\Registry;

use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionProperty;

class Registry
{
    /**
     * The registered request authorizers
     *
     * @var \Illuminate\Support\Collection
     */
    protected $authorizers;

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
     * A list of event listeners
     *
     * @var \Illuminate\Support\Collection
     */
    protected $eventListeners;

    /**
     * A list of mapped event names
     *
     * @var \Illuminate\Support\Collection
     */
    protected $eventMaps;

    /**
     * A list of event subscribers
     *
     * @var \Illuminate\Support\Collection
     */
    protected $eventSubscribers;

    /**
     * A list of model factories
     *
     * @var \Illuminate\Support\Collection
     */
    protected $modelFactories;

    /**
     * A list of MorphMaps for Models
     *
     * @var \Illuminate\Support\Collection
     */
    protected $morphMaps;

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
     * The registered policies
     *
     * @var \Illuminate\Support\Collection
     */
    protected $policies;

    /**
     * The policy registrars
     *
     * @var \Illuminate\Support\Collection
     */
    protected $policyRegistrars;

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
     * The list of custom routes
     *
     * @var \Illuminate\Support\Collection
     */
    protected $customRoutes;

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
     * The registered request sanitizers
     *
     * @var \Illuminate\Support\Collection
     */
    protected $sanitizers;

    /**
     * The registered request validators
     *
     * @var \Illuminate\Support\Collection
     */
    protected $validators;

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

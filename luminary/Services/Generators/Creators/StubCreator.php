<?php

namespace Luminary\Services\Generators\Creators;

use Luminary\Services\ApiLoader\Helpers\Directory;
use Luminary\Services\Generators\Contracts\CreatorInterface;
use Luminary\Services\Filesystem\App\Storage;

abstract class StubCreator implements CreatorInterface
{
    /**
     * The root namespace for the class
     * being created
     *
     * @var string
     */
    protected $rootNamespace;

    /**
     * The class name to create
     *
     * @var string
     */
    protected $name;

    /**
     * The File path of the
     * class being created
     *
     * @var string
     */
    protected $path;

    /**
     * An array of replacable attributes
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new controller creator command instance.
     *
     * @param string $name
     * @param string $path
     */
    public function __construct(string $name, string $path)
    {
        $this->setName($name);
        $this->setPath($path);
        $this->setRootNamespace($this->path);
    }

    /**
     * Create a new seed at the given path.
     *
     * @param string $name
     * @param string $path
     * @param array $attributes
     * @return StubCreator
     */
    public static function create(string $name, string $path, array $attributes = []) :StubCreator
    {
        $self = new static($name, $path);

        if ($self->alreadyExists()) {
            return $self;
        }

        $self->setAttributes($attributes);
        $path = $self->getPath($name);

        Storage::makeDirectory($self->getPath(), true);
        Storage::put($path, $self->buildClass());

        return $self;
    }

    /**
     * Get the name property
     *
     * @return string
     */
    public function name() :string
    {
        return $this->name;
    }

    /**
     * Set the class name as
     * studly case
     *
     * @param string $name
     */
    public function setName(string $name) :void
    {
        $this->name = studly_case($name);
    }

    /**
     * Get the root namespace property
     *
     * @return string
     */
    public function rootNamespace() :string
    {
        return $this->rootNamespace;
    }

    /**
     * Set the root namespace
     *
     * @param string $path
     * @return void
     */
    public function setRootNamespace(string $path) :void
    {
        $this->rootNamespace = Directory::make($path)->namespace();
    }

    /**
     * Get an attribute value
     *
     * @param $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        return array_get($this->attributes, $key);
    }

    /**
     * Set an attribute value
     *
     * @param string $name
     * @param $value
     * @return void
     */
    public function setAttribute(string $name, $value) :void
    {
        if (! array_key_exists($name, $this->attributes)) {
            return;
        }

        $method = camel_case('set_'.$name.'_attribute');

        $this->attributes[$name] = method_exists($this, $method)
            ? $this->{$method}($name, $value)
            : $value;
    }

    /**
     * Get the attributes
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $keys = array_keys($this->attributes);
        $attributes = array_only($attributes, $keys);

        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * Set the root directory path
     *
     * @param $path
     * @return void
     */
    protected function setPath($path) :void
    {
        Storage::makeDirectory($path, true);

        $this->path = $path;
    }

    /**
     * Get the root path or a path
     * from the root path
     *
     * @param null $name
     * @return string
     */
    protected function getPath($name = null) :string
    {
        $file = $name ? '/'.$this->name.'.php' : '';
        return $this->path.$file;
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @return string
     */
    protected function namespace() :string
    {
        return $this->rootNamespace() . '\\' . $this->name();
    }

    /**
     * Determine if the class already exists.
     *
     * @return bool
     */
    protected function alreadyExists() :bool
    {
        return Storage::exists($this->getPath($this->name));
    }

    /**
     * Build the class
     *
     * @return string
     */
    protected function buildClass() :string
    {
        $stub = Storage::get($this->getStub());

        return $this->replaceNamespace($stub)->replaceClass($stub)->replaceAttributes($stub);
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @return \Luminary\Services\Generators\Creators\StubCreator $this
     */
    protected function replaceNamespace(string &$stub) :StubCreator
    {
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace'],
            [$this->rootNamespace(), $this->rootNamespace()],
            $stub
        );

        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @return \Luminary\Services\Generators\Creators\StubCreator $this
     */
    protected function replaceClass(string &$stub) :StubCreator
    {
        $name = $this->name();
        $class = str_replace($this->namespace().'\\', '', $name);

        $stub =  str_replace('DummyClass', $class, $stub);

        return $this;
    }

    /**
     * Replace the attributes in a stub file
     *
     * @param string $stub
     * @return mixed
     */
    protected function replaceAttributes(string $stub)
    {
        $attributes = collect($this->getAttributes());
        $keys = $attributes->keys()
            ->map(function ($key) {
                return '{{'.$key.'}}';
            })->toArray();
        $values = $attributes->values()->toArray();

        return str_replace($keys, $values, $stub);
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub() :string;
}

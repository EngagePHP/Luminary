<?php

namespace Luminary\Services\ApiLoader\Helpers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Luminary\Services\ApiLoader\Exceptions\DirectoryNotFound;
use Luminary\Services\Filesystem\App\Storage;

class Directory
{
    /**
     * The directory path
     *
     * @var string
     */
    protected $path;

    /**
     * Directory constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Return a new Directory instance
     *
     * @param $path
     * @return static
     */
    public static function make(string $path)
    {
        $directory = new static($path);

        $directory->validate();

        return $directory;
    }

    /**
     * Return a list of classes from
     * within a directory
     *
     * @return array
     */
    public function classes()
    {
        $files = collect($this->files());

        return $files->filter(
            function ($path) {
                return is_class($path);
            }
        )->transform(
            function ($path) {
                $name = $this->name($path);
                return $this->class($name);
            }
        )->toArray();
    }

    /**
     * Create a namespaced classname by path
     *
     * @param string $path
     * @return string
     */
    public function class(string $path) :string
    {
        $name = $this->name($path);
        return $this->namespace($name);
    }

    /**
     * Check if a file path exists
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path) :bool
    {
        $path = $this->path($path);
        return is_file($path);
    }

    /**
     * Include a php file returning
     * an array
     *
     * @param string $path
     * @return array
     */
    public function includeArray(string $path) :array
    {
        return $this->exists($path)
            ? (array) include $this->path($path)
            : [];
    }

    /**
     * Return the name of the directory
     *
     * @param string|null $path
     * @return string
     */
    public function name(string $path = null) :string
    {
        $path = $path ?: $this->path;
        return basename($path);
    }

    /**
     * Return the name of the directory
     * in snake case
     *
     * @return string
     */
    public function snakeName()
    {
        return snake_case($this->name());
    }

    /**
     * Get the directory Namespace
     *
     * @param string $name
     * @param string $ext
     * @return string
     */
    public function namespace(string $name = '', string $ext = '.php') :string
    {
        $path = $this->path($name);
        $name = str_replace([app_path(), $ext], '', $path);
        $name = ltrim($name, '/');
        $name = str_replace('/', '\\', $name);

        return 'Api\\'.$name;
    }

    /**
     * Return the directory path
     *
     * @param string $path
     * @return string
     */
    public function path(string $path = '') :string
    {
        return $this->path.($path ? '/'.$path : $path);
    }

    /**
     * Route method calls to the static filesystem instance
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $arguments = [$this->path];
        return call_user_func_array([Storage::class, $name], $arguments);
    }

    /**
     * Validate the path as a directory
     *
     * @throws \Luminary\Services\ApiLoader\Exceptions\DirectoryNotFound
     */
    protected function validate()
    {
        if ($this->isDirectory()) {
            return;
        }

        throw new DirectoryNotFound('The path does not exist or is not a directory');
    }
}

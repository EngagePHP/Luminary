<?php

namespace Luminary\Services\ApiResponse;

use Illuminate\Http\Request;

class ResponseHelper
{
    /**
     * The static Request instance
     *
     * @var \Illuminate\Http\Request
     */
    protected static $request;

    /**
     * Get the current self url
     *
     * @return string
     */
    public static function self() :string
    {
        return str_replace('http://:', 'http://localhost', static::request()->fullUrl());
    }

    /**
     * Get the root URL for the request.
     *
     * @return string
     */
    public static function root() :string
    {
        $root = static::request()->root();
        return $root == 'http://:' ? 'http://localhost' : $root;
    }

    /**
     * Get the base url without query
     *
     * @return string
     */
    public static function url() :string
    {
        return static::request()->url();
    }

    /**
     * Get the request query string
     *
     * @return null|string
     */
    public static function queryString()
    {
        return static::request()->getQueryString();
    }

    /**
     * Return the resource name
     *
     * @return string
     */
    public static function resource() :string
    {
        return (string) static::request()->segment(1);
    }

    /**
     * Return the resource url self link
     *
     * @param null $resourceId
     * @param string $resource
     * @return string
     */
    public static function resourceSelf($resourceId = null, string $resource = null) :string
    {
        $resource = $resource ?: static::resource();
        $resource = static::formatResource($resource);
        return static::generateUrl([$resource, $resourceId]);
    }

    /**
     * Format a resource name with dashes
     *
     * @param string $resource
     * @return string
     */
    public static function formatResource(string $resource) :string
    {
        return str_replace(['_', ' '], '-', $resource);
    }

    /**
     * Generate a resource relationship link set
     *
     * @param string $resource
     * @param string $resourceId
     * @param string $relationship
     * @param bool $plural
     * @return array
     */
    public static function generateRelationshipLinks(
        string $resource,
        string $resourceId,
        string $relationship,
        bool $plural = true
    ) :array {
        $resource = static::formatResource($resource);
        $relationship = static::formatResource($relationship);
        $relationship = $plural ? str_plural($relationship) : str_singular($relationship);

        // Convert relationship name to slug
        $relationship = str_slug(snake_case($relationship));

        return [
            'self' => static::generateUrl([$resource, $resourceId, 'relationships', $relationship]),
            'related' => static::generateUrl([$resource, $resourceId, $relationship])
        ];
    }

    /**
     * Generate a full url string by
     * given array
     *
     * @param array $path
     * @return string
     */
    public static function generateUrl(array $path)
    {
        $path = array_map(function ($segment) {
            return static::formatResource($segment ?: '');
        }, $path);

        $path = array_merge([static::root()], [config('luminary.location')], $path);

        return implode('/', array_filter($path));
    }

    /**
     * Return the request object
     *
     * @return Request
     */
    public static function request() :Request
    {
        return app(Request::class);
    }
}

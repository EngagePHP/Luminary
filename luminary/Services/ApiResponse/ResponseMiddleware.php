<?php

namespace Luminary\Services\ApiResponse;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Luminary\Services\ApiResponse\Serializers\ArraySerializer;
use Luminary\Services\ApiResponse\Serializers\CollectionRelatedSerializer;
use Luminary\Services\ApiResponse\Serializers\CollectionRelationshipSerializer;
use Luminary\Services\ApiResponse\Serializers\CollectionSerializer;
use Luminary\Services\ApiResponse\Serializers\EmptySerializer;
use Luminary\Services\ApiResponse\Serializers\ModelRelatedSerializer;
use Luminary\Services\ApiResponse\Serializers\ModelRelationshipSerializer;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;
use Luminary\Services\ApiResponse\Serializers\SerializerInterface;

class ResponseMiddleware
{
    /**
     * Set the bool for whether the response
     * format should be for relationships
     *
     * @var bool
     */
    public static $relationshipResponse = false;

    /**
     * Set the bool for whether the response
     * format should be for related resources
     *
     * @var bool
     */
    public static $relatedResponse = false;

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if ($response->headers->has('content-disposition')) {
            return $response;
        }

        if (! $response instanceof JsonResponse) {
            $serializer = $this->content($response, $request);

            $this->setResponseTime($serializer);

            $response->setContent($serializer->serialize())
                ->header('Content-Type', config('luminary.contentType'));

            $this->setResponseStatus($request, $response);
        }

        return $response;
    }

    /**
     * Tell if the current response for a relationship
     * request.
     *
     * @return bool
     */
    public function isRelationshipResponse() :bool
    {
        return static::$relationshipResponse;
    }

    /**
     * Tell if the current response for a related
     * request.
     *
     * @return bool
     */
    public function isRelatedResponse() :bool
    {
        return static::$relatedResponse;
    }

    /**
     * Return the correct content serializer
     *
     * @param Response $response
     * @param  \Illuminate\Http\Request  $request
     * @return SerializerInterface
     */
    public function content($response, $request) :SerializerInterface
    {
        $original = $response->getOriginalContent();

        switch (true) {
            case $original instanceof Collection:
                return $this->getCollectionSerializer($original, $request);
                break;
            case $original instanceof Model:
                return $this->getModelSerializer($original, $request);
                break;
            case is_bool($original):
            case is_null($original):
                return new EmptySerializer;
                break;
            case is_array($original):
            default:
                return new ArraySerializer((array) $original);
                break;
        }
    }

    /**
     * Get the correct model serializer
     *
     * @param Model $model
     * @param $request
     * @return ModelRelatedSerializer|ModelRelationshipSerializer|ModelSerializer
     */
    public function getModelSerializer(Model $model, $request)
    {
        if($this->isRelationshipResponse()) {
            return new ModelRelationshipSerializer($model, $request);
        }

        if($this->isRelatedResponse()) {
            return new ModelRelatedSerializer($model, $request);
        }

        return new ModelSerializer($model);
    }

    /**
     * Get the correct model serializer
     *
     * @param Collection $collection
     * @param $request
     * @return CollectionRelatedSerializer|CollectionRelationshipSerializer|CollectionSerializer
     */
    public function getCollectionSerializer(Collection $collection, $request)
    {
        if($this->isRelationshipResponse()) {
            return new CollectionRelationshipSerializer($collection, $request);
        }

        if($this->isRelatedResponse()) {
            return new CollectionRelatedSerializer($collection, $request);
        }

        return new CollectionSerializer($collection);
    }

    /**
     * Set the response time in the meta
     *
     * @param SerializerInterface $serializer
     */
    public function setResponseTime(SerializerInterface $serializer)
    {
        $time = microtime(true) - $this->appStart();

        $responseTime = $time > 1 ? round($time, 2) . ' seconds' : floor(($time) * 1000) . ' milliseconds';

        $serializer->addResponseMeta('response_time', $responseTime);
    }

    /**
     * Get the app start time
     *
     * @return int
     */
    public function appStart()
    {
        return app('config')->get('app.start', microtime(true));
    }

    /**
     * Set the response status
     *
     * @param $request
     * @param Response $response
     */
    public function setResponseStatus($request, Response $response) :void
    {
        switch ($request->getMethod()) {
            case 'POST':
                $this->setCreateResponseStatus($response);
                break;
            case 'PATCH':
                $this->setUpdateResponseStatus($response);
                break;
            case 'DELETE':
                $this->setDeleteResponseStatus($response);
                break;
            case 'GET':
            default:
                $this->setOkResponseStatus($response);
        }
    }

    /**
     * Set the OK response status
     *
     * @param Response $response
     * @return void
     */
    public function setOkResponseStatus(Response $response) :void
    {
        $response->setStatusCode(200);
    }

    /**
     * Set the response status base on created content
     *
     * @todo Figure out how to manage queue responses | 202 Accepted
     * @param Response $response
     */
    public function setCreateResponseStatus(Response $response) :void
    {
        $content = $response->getContent();

        switch (true) {
            case empty($content):
                $status = 204;
                break;
            default:
                $status = 201;
                $response->header('LOCATION', array_get($content, 'data.links.self'));
        }

        $response->setStatusCode($status);
    }

    /**
     * Set the Update response status
     *
     * @param Response $response
     * @return void
     */
    public function setUpdateResponseStatus(Response $response) :void
    {
        $status = empty($response->getContent()) ? 204 : 200;
        $response->setStatusCode($status);
    }

    /**
     * Set the Delete response status
     *
     * @param Response $response
     * @return void
     */
    public function setDeleteResponseStatus(Response $response) :void
    {
        $response->setContent('');
        $response->setStatusCode(204);
    }
}

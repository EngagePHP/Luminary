<?php

namespace Luminary\Services\ApiResponse;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Luminary\Database\Eloquent\Collection;
use Luminary\Services\ApiResponse\Serializers\ArraySerializer;
use Luminary\Services\ApiResponse\Serializers\CollectionSerializer;
use Luminary\Services\ApiResponse\Serializers\ModelSerializer;
use Luminary\Services\ApiResponse\Serializers\SerializerInterface;

class ResponseMiddleware
{
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

        $serializer = $this->content($response);

        $this->setResponseTime($serializer);

        $response->setContent($serializer->serialize());

        return $response;
    }

    /**
     * Return the correct content serializer
     *
     * @param Response $response
     * @return SerializerInterface
     */
    public function content(Response $response) :SerializerInterface
    {
        $original = $response->getOriginalContent();

        switch (true) {
            case $original instanceof Collection:
                return new CollectionSerializer($original);
                break;
            case $original instanceof Model:
                return new ModelSerializer($original);
                break;
            case is_array($original):
            default:
                return new ArraySerializer((array) $original);
                break;
        }
    }

    /**
     * Set the response time in the meta
     *
     * @param SerializerInterface $serializer
     */
    public function setResponseTime(SerializerInterface $serializer)
    {
        $time = microtime(true) - $this->appStart();
        $responseTime = $time > 1 ? $time . ' seconds' : ($time * 1000) . ' milliseconds';

        $serializer->addMeta('response_time', $responseTime);
    }

    /**
     * Get the app start time
     *
     * @return int
     */
    public function appStart() :int
    {
        return app('config')->get('app.start');
    }
}

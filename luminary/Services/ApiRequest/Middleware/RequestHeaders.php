<?php

namespace Luminary\Services\ApiRequest\Middleware;

use Closure;
use Luminary\Services\ApiRequest\Exceptions\MediaTypeParametersNotAllowed;
use Luminary\Services\ApiRequest\Exceptions\UnsupportedMediaType;

class RequestHeaders
{
    /**
     * The vendor tree string
     *
     * @var string
     */
    protected $vendorTree = 'application/vnd';

    /**
     * The producer string
     *
     * @var string
     */
    protected $producer = 'api';

    /**
     * The request media type
     *
     * @var string
     */
    protected $mediaType = 'json';

    /**
     * Get the accepted vendor tree property
     *
     * @return string
     */
    public function vendorTree()
    {
        return $this->vendorTree;
    }

    /**
     * Get the accepted producer property
     *
     * @return string
     */
    public function producer()
    {
        return $this->producer;
    }

    /**
     * Get the accepted media type property
     *
     * @return string
     */
    public function mediaType()
    {
        return $this->mediaType;
    }

    /**
     * Return the fully concatenated
     * acceptable media type
     *
     * @return string
     */
    public function acceptedHeader() :string
    {
        return $this->vendorTree() . '.' . $this->producer() . '+' . $this->mediaType();
    }

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $accept = $request->header('accept');
        $content = $request->header('content-type');


        $this->handleContentType($content);
        $this->handleAcceptHeaders($accept);

        return $next($request);
    }

    /**
     * Validate the content-type header
     *
     * @param $contentType
     */
    protected function handleContentType($contentType) :void
    {
        if ($this->isStrictMatch($contentType)) {
            return;
        }

        $this->handleHeader($contentType, 'Content-Type');
    }

    /**
     * Validate the accept headers
     *
     * @param string $header
     * @return void
     */
    protected function handleAcceptHeaders(string $header) :void
    {
        collect(explode(',', $header))->each(
            function ($accept) {
                if ($this->isVendor($accept)) {
                    $this->handleHeader($accept, 'Accept');
                }
            }
        );
    }

    /**
     * Handle an individual header validation
     *
     * @param $header
     * @param $type
     */
    protected function handleHeader($header, $type) :void
    {
        if (! $this->isCorrectVendor($header) || ! $this->hasCorrectMediaType($header)) {
            $this->throwUnsupportedMediaTypeException($type);
        } elseif ($this->hasAdditionalParameters($header)) {
            $this->throwMediaTypeParametersNotAllowException($type);
        }
    }

    /**
     * Check if the header is a strict
     * match to the accepted header
     *
     * @param string $header
     * @return bool
     */
    public function isStrictMatch(string $header) :bool
    {
        return $header === $this->acceptedHeader();
    }

    /**
     * Does the header have vendor name
     *
     * @param string $header
     * @return bool
     */
    public function isVendor(string $header) :bool
    {
        preg_match('#' . $this->vendorTree() . '\.' . $this->producer() . '#', $header, $matches);

        return (count($matches));
    }

    /**
     * Does the header have the correct vendor
     *
     * @param string $header
     * @return bool
     */
    public function isCorrectVendor(string $header) :bool
    {
        return $this->isVendor($header);
    }

    /**
     * Does the header have the correct media type?
     *
     * @param string $header
     * @return bool
     */
    public function hasCorrectMediaType(string $header) :bool
    {
        preg_match('#' . $this->vendorTree() . '\.' . $this->producer() . '\+(\w+)#', $header, $matches);

        $matches = array_slice($matches, 1);

        return count($matches) && head($matches) === 'json';
    }

    /**
     * Does the header have additional parameters?
     *
     * @param string $header
     * @return bool
     */
    public function hasAdditionalParameters(string $header) :bool
    {
        $pattern = preg_quote($this->acceptedHeader(), '/');

        preg_match('/' . $pattern . '/', $header, $matches);

        $count = count($matches);

        if ($count) {
            $count = collect(explode(';', $header))->filter(function ($item) {
                return trim($item);
            })->slice(1)->count();
        }

        return (bool) $count;
    }

    /**
     * Throw a new unsupported media type exception
     *
     * @param string $type
     * @throws \Luminary\Services\ApiRequest\Exceptions\UnsupportedMediaType
     */
    public function throwUnsupportedMediaTypeException(string $type)
    {
        $message = 'The media type for ' . $type . ' must be: ' . $this->acceptedHeader();
        throw new UnsupportedMediaType($type, $message);
    }

    /**
     * Throw a new media type parameters not allow exception
     *
     * @param string $type
     * @throws \Luminary\Services\ApiRequest\Exceptions\MediaTypeParametersNotAllowed
     */
    public function throwMediaTypeParametersNotAllowException(string $type)
    {
        $message = 'Media type parameters are not allowed. Only ';
        $message .= $this->acceptedHeader() . ' is allowed for ' . $type . 'Header.';

        throw new MediaTypeParametersNotAllowed($type, $message);
    }
}

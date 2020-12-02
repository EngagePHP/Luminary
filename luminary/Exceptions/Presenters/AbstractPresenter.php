<?php

namespace Luminary\Exceptions\Presenters;

use Exception;
use Illuminate\Http\JsonResponse;
use Luminary\Exceptions\Contracts\PresenterInterface;

abstract class AbstractPresenter implements PresenterInterface
{
    /**
     * The Exception instance
     *
     * @var Exception
     */
    protected $exception;

    /**
     * Default application response headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * DefaultPresenter constructor.
     *
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
    }

    /**
     * Return the error message
     *
     * @return string
     */
    public function message() :string
    {
        return $this->exception->getMessage();
    }

    /**
     * Render the json response
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render() :JsonResponse
    {
        $headers = array_merge($this->headers, ['Content-Type' => config('luminary.contentType')]);
        return response()->json(['errors' => $this->response()], $this->status(), $headers);
    }

    /**
     * Return the error response array
     *
     * @return array
     */
    abstract public function response() :array;

    /**
     * Return the http status code
     *
     * @return int
     */
    public function status() :int
    {
        $status = property_exists($this->exception, 'status') ? $status = $this->exception->status : null;

        if($status) {
            return (int) $status;
        } else if($code = $this->exception->getCode()) {
            return (int) $code;
        } else {
            return 500;
        }
    }

    /**
     * Return the error response title
     *
     * @return null|string
     */
    public function title() :string
    {
        return $this->getExceptionAttribute('title', 'An unknown error has occurred');
    }

    /**
     * Return the error response title
     *
     * @return null|string
     */
    public function source()
    {
        return $this->getExceptionAttribute('source');
    }

    /**
     * Get an exceptio attribute if it exists
     *
     * @param $name
     * @param null $default
     * @return null
     */
    protected function getExceptionAttribute($name, $default = null)
    {
        $method = 'get'.studly_case($name);

        return method_exists($this->exception, $method)
            ? $this->exception->{$method}()
            : $default;
    }
}

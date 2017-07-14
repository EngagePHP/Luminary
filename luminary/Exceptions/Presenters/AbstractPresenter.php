<?php

namespace Luminary\Exceptions\Presenters;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class AbstractPresenter implements PresenterInterface
{
    /**
     * The Exception instance
     *
     * @var Exception
     */
    protected $exception;

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
        $headers = [
            'Content-Type' => 'application/vnd.api+json'
        ];

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
        return (int) $this->exception->getCode();
    }

    /**
     * Return the error response title
     *
     * @return string
     */
    public function title() :string
    {
        return 'An unknown error has occurred';
    }
}

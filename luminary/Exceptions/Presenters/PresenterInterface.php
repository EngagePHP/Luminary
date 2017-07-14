<?php

namespace Luminary\Exceptions\Presenters;

use Exception;
use Illuminate\Http\JsonResponse;

interface PresenterInterface
{
    /**
     * DefaultPresenter constructor.
     *
     * @param Exception $exception
     */
    public function __construct(Exception $exception);

    /**
     * Return the error message
     *
     * @return string
     */
    public function message() :string;

    /**
     * Render the json response
     * @return JsonResponse
     */
    public function render() :JsonResponse;

    /**
     * Return the error response array
     *
     * @return array
     */
    public function response() :array;

    /**
     * Return the http status code
     *
     * @return int
     */
    public function status() :int;

    /**
     * Return the error response title
     *
     * @return string
     */
    public function title() :string;
}

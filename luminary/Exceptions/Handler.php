<?php

namespace Luminary\Exceptions;

use Exception;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * A list of presenters by
     * Exception type
     *
     * @var array
     */
    protected $presenters = [
        MultiException::class => Presenters\MultiExceptionPresenter::class,
        HttpException::class => Presenters\HttpExceptionPresenter::class,
        UnauthorizedException::class => Presenters\UnauthorizedPresenter::class
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        $presenter = $this->getPresenter($e);

        return $presenter->render();
    }

    /**
     * Get the correct presenter to render
     *
     * @param Exception $e
     * @return mixed
     */
    public function getPresenter(Exception $e)
    {
        $presenter = Presenters\DefaultPresenter::class;

        foreach ($this->presenters as $exception => $class) {
            if ($e instanceof $exception) {
                $presenter = $class;
                break;
            }
        }

        return new $presenter($e);
    }
}

<?php

namespace Luminary\Services\FormRequests;

use Laravel\Lumen\Http\Redirector;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Luminary\Services\Auth\Contracts\AuthorizesWhenResolved;
use Luminary\Services\Sanitation\Contracts\SanitizesWhenResolved;
use Symfony\Component\HttpFoundation\Request;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->afterResolving(AuthorizesWhenResolved::class, function ($resolved) {
            $resolved->authorizeInstance();
        });

        $this->app->afterResolving(SanitizesWhenResolved::class, function ($resolved) {
            $resolved->sanitizeInstance();
        });

        $this->app->afterResolving(ValidatesWhenResolved::class, function ($resolved) {
            $resolved->validate();
        });

        $this->app->resolving(BaseRequest::class, function ($request, $app) {
            $this->initializeRequest($request, $app['request']);

            $request->setContainer($app)->setRedirector($app->make(Redirector::class));
        });
    }

    /**
     * Initialize the form request with data from the given request.
     *
     * @param  \Luminary\Services\FormRequests\BaseRequest  $form
     * @param  \Symfony\Component\HttpFoundation\Request  $current
     * @return void
     */
    protected function initializeRequest(BaseRequest $form, Request $current)
    {
        $files = $current->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $form->initialize(
            $current->query->all(),
            $current->request->all(),
            $current->attributes->all(),
            $current->cookies->all(),
            $files,
            $current->server->all(),
            $current->getContent()
        );

        $form->setJson($current->json());

        $form->setType($current->type());
        $form->setResource($current->resource());
        $form->setData($current->data());
        $form->setRelationships($current->relationships());
        $form->setRelated($current->isRelated());
        $form->setRelationship($current->isRelationship());

        if ($session = $current->getSession()) {
            $form->setLaravelSession($session);
        }

        $form->setUserResolver($current->getUserResolver());

        $form->setRouteResolver($current->getRouteResolver());
    }
}

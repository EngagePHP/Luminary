<?php

namespace Luminary\Services\ApiRequest\Traits;

use Illuminate\Http\Request;

trait HasCustomRequest
{
    /**
     * Inject the new request
     *
     * @param string $class
     * @param Request $request
     * @return Request
     */
    protected function injectRequest(string $class, Request $request = null) :Request
    {
        $request = $request ?: app('reqest');

        app()->singleton('request', function () use ($request, $class) {
            $class = new $class();

            $this->initializeRequest($class, $request);
            $this->customizeRequest($class);

            return $class;
        });

        return app('request');
    }

    /**
     * Get the request params to inject
     *
     * @param Request $request
     * @return array
     */
    protected function getRequestParams(Request $request) :array
    {
        $files = $request->files->all();
        $files = is_array($files) ? array_filter($files) : $files;

        return [
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $files,
            $request->server->all(),
            $request->getContent()
        ];
    }

    /**
     * Initialize the custom class with
     * all the data from the original request
     *
     * @param Request $request
     * @param Request $current
     *
     * @return void
     */
    protected function initializeRequest(Request $request, Request $current) :void
    {
        $params = $this->getRequestParams($current);

        $request->initialize(...$params);
        $request->setJson($current->json());

        if ($session = $current->getSession()) {
            $request->setLaravelSession($session);
        }

        $request->setUserResolver($current->getUserResolver());
        $request->setRouteResolver($current->getRouteResolver());
    }

    /**
     * An empty method for customizing the
     * injected request before being returned
     *
     * @param Request $request
     */
    protected function customizeRequest(Request $request)
    {
        //
    }
}

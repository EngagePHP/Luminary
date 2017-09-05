<?php

namespace Luminary\Services\FormRequests;

use Luminary\Services\Auth\Authorize as DefaultAuthorize;
use Luminary\Services\Auth\AuthorizesWhenResolvedTrait;
use Luminary\Services\Auth\Contracts\Authorize;
use Luminary\Services\Auth\Contracts\AuthorizesWhenResolved;

abstract class AuthRequest extends BaseRequest implements AuthorizesWhenResolved
{
    use AuthorizesWhenResolvedTrait;

    /**
     * The authorize instance.
     *
     * @var \Luminary\Services\Auth\Contracts\Authorize
     */
    protected $authorize;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    abstract public function authorize() :bool;

    /**
     * Set the Authorize instance
     *
     * @param Authorize $authorize
     */
    public function setAuthorize(Authorize $authorize)
    {
        $this->authorize = $authorize;
    }

    /**
     * Get the Authorize instance
     *
     * @return \Luminary\Services\Auth\Contracts\Authorize
     */
    public function getAuthorize() :Authorize
    {
        return $this->authorize;
    }

    /**
     * Prepare the instance for authorization.
     *
     * @return void
     */
    protected function prepareForAuthorization()
    {
        $authorize = $this->resolveAuthorizationInstance();

        $this->setAuthorize($authorize);
    }

    /**
     * Resolve the Authorization instance
     *
     * @return \Luminary\Services\Auth\Contracts\Authorize
     */
    protected function resolveAuthorizationInstance() :Authorize
    {
        $app = app();
        $class = $app->authorizer($this->resource(), DefaultAuthorize::class);

        return $app->make($class);
    }
}

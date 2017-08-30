<?php

namespace Luminary\Services\Auth;

use Illuminate\Validation\UnauthorizedException;

trait AuthorizesWhenResolvedTrait
{
    /**
     * Authorize the class instance.
     *
     * @return void
     */
    public function authorizeInstance()
    {
        $this->prepareForAuthorization();

        if (! $this->passesAuthorization()) {
            $this->failedAuthorization();
        }
    }

    /**
     * Prepare the instance for authorization.
     *
     * @return void
     */
    protected function prepareForAuthorization()
    {
        // no default action
    }


    /**
     * Determine if the request passes the authorization check.
     *
     * @return bool
     */
    protected function passesAuthorization()
    {
        if (method_exists($this, 'authorize')) {
            return $this->authorize();
        }

        return true;
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\UnauthorizedException
     */
    protected function failedAuthorization()
    {
        throw new UnauthorizedException;
    }
}

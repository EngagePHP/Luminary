<?php

namespace Luminary\Services\Auth\Repositories;

use Illuminate\Validation\UnauthorizedException;

class AuthRepository
{
    /**
     * Return the application Guard instance
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public static function guard()
    {
        return app('auth')->guard();
    }

    /**
     * Create a new default expire time
     *
     * @return int
     */
    public static function expires()
    {
        return static::TTL() * 60;
    }

    /**
     * Attempt user login and token generation
     *
     * @param array $credentials
     * @return array
     */
    public static function login(array $credentials)
    {
        $token = static::guard()->attempt($credentials);

        if (!$token) {
            throw new UnauthorizedException('The username/email or assword are incorrect');
        }

        return static::tokenResponse($token);
    }

    /**
     * Attempt logout
     *
     * @return bool
     */
    public static function logout()
    {
        return static::guard()->logout();
    }

    /**
     * Refresh the logged in user token
     *
     * @return array
     */
    public static function refresh()
    {
        $token = static::guard()->refresh();
        return static::tokenResponse($token);
    }

    /**
     * Generate the JWT Response
     *
     * @param $token
     * @return array
     */
    public static function tokenResponse($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => static::expires()
        ];
    }

    /**
     * Generate a time to live
     * in minutes
     *
     * @return int
     */
    public static function TTL()
    {
        return static::guard()->factory()->getTTL();
    }

    /**
     * Return the authenticated user
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public static function user()
    {
        return static::guard()->user();
    }
}

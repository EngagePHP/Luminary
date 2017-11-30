<?php

namespace Luminary\Services\Auth\Controllers;

use Luminary\Http\Controllers\Controller;
use Luminary\Services\ApiRequest\ApiRequest as Request;
use Luminary\Services\Auth\Repositories\AuthRepository;

class AuthController extends Controller
{
    /**
     * Get a JWT token via given credentials.
     *
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $input = $request->isJson() ? $request->json() : $request;
        $credentials = array_only($input->all(), ['email', 'username', 'password']);
        $response = AuthRepository::login($credentials);

        return response()->json($response);
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function user(Request $request)
    {
        return AuthRepository::user();
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        AuthRepository::logout();
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $response = AuthRepository::refresh();
        return response()->json($response);
    }
}

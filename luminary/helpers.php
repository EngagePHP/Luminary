<?php

if (! function_exists('app_path')) {
    /**
     * Get the path to the base of the api
     *
     * @param  string  $path
     * @return string
     */
    function app_path(string $path = '')
    {
        return app()->basePath('api').($path ? '/'.$path : $path);
    }
}

if (! function_exists('is_class')) {
    /**
     * Determines whether a file
     * has a class or namespace
     *
     * @param  string  $path
     * @return boolean
     */
    function is_class(string $path = '')
    {
        $content = file_get_contents($path);
        $tokens = token_get_all($content);
        $accept = ['T_CLASS', 'T_NAMESPACE'];

        return collect($tokens)
            ->filter(
                function ($token) use ($accept) {
                    $token = is_array($token) ? token_name(head($token)) : null;
                    return $token && in_array($token, $accept);
                }
            )
            ->count() > 0;
    }
}

if (! function_exists('env_list')) {
    /**
     * Gets the value of an environment variable,
     * and returns as an array
     * @param  string $key
     * @param  mixed $default
     * @param string $separator
     * @return mixed
     */
    function env_list($key, $default = null, $separator = ',') :array
    {
        $value = env($key, $default);
        return ! is_null($value) ? explode($separator, $value) : [];
    }
}

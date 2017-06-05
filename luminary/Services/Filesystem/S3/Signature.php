<?php

namespace Luminary\Services\Filesystem\S3;

use EddTurtle\DirectUpload\Signature as DirectUploadSignature;

class Signature extends DirectUploadSignature
{
    use PolicyGenerator;

    /**
     * The base laravel config namespace
     *
     * @var string
     */
    private static $config;

    /**
     * Factory to create an instance
     * of the Signature Class
     *
     * @param string $config
     * @return \EddTurtle\DirectUpload\Signature
     */
    public static function make($config = 's3')
    {
        static::$config = $config;

        return new static(...static::params());
    }

    /**
     * Retrieve an S3 Config value
     *
     * @param string $key
     * @return mixed
     */
    public static function config($key)
    {
        return config(static::$config . '.' . $key);
    }

    /**
     * Create an array of parameters
     * for instantiating the Signature Class
     *
     * @return array
     */
    private static function params()
    {
        return array_merge(
            static::credentials(),
            [ static::options() ]
        );
    }

    /**
     * Create the credentials array
     *
     * @return array
     */
    private static function credentials()
    {
        extract(static::config('credentials'));
        $creds = compact('key', 'secret', 'bucket', 'region');

        return array_values($creds);
    }

    /**
     * Get the options from the config file
     *
     * @return array
     */
    private static function options()
    {
        return static::config('options');
    }
}

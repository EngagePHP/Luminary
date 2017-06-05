<?php

namespace Luminary\Services\Filesystem\S3;

use Luminary\Services\Filesystem\AbstractFilesystem;

class Storage extends AbstractFilesystem
{
    /**
     * Default storage option
     *
     * @var string
     */
    protected static $type = 's3-encrypted';

    /**
     * Set the encrypted storage credentials
     *
     * @return static
     */
    public static function encrypted()
    {
        static::$type = 's3-encrypted';

        return new static;
    }

    /**
     * Set the decrypted storage credentials
     *
     * @return static
     */
    public static function decrypted()
    {
        static::$type = 's3';

        return new static;
    }

    /**
     * Return an instance of EddTurtle Signature
     *
     * @return \EddTurtle\DirectUpload\Signature
     */
    public static function form()
    {
        return Signature::make();
    }

    /**
     * Return the s3 object pre-signed url
     *
     * @param string $filename
     * @param string $expires
     * @return string
     */
    public static function url($filename, $expires = "+10 minutes")
    {
        $client = static::getClient();
        $args = [
            'Bucket' => config('filesystems.disks.'. static::$type .'.bucket'),
            'Key'    => static::getFullPath($filename)
        ];

        $command = $client->getCommand('GetObject', $args);
        $request = $client->createPresignedRequest($command, $expires);

        return (string) $request->getUri();
    }
}

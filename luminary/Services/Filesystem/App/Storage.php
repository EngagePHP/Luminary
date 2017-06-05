<?php

namespace Luminary\Services\Filesystem\App;

use Luminary\Services\Filesystem\AbstractFilesystem;

class Storage extends AbstractFilesystem
{
    /**
     * Default storage option
     *
     * @var string
     */
    protected static $type;

    /**
     * Get the Storage Adapter
     *
     * @return \Illuminate\Contracts\Filesystem\Filesystem
     */
    protected static function getStorage()
    {
        return app('files');
    }
}

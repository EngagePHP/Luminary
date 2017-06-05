<?php

namespace Luminary\Services\Filesystem\Local;

use Luminary\Services\Filesystem\AbstractFilesystem;

class Storage extends AbstractFilesystem
{
    /**
     * Default storage option
     *
     * @var string
     */
    protected static $type = 'local';
}

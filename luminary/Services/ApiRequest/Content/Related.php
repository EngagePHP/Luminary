<?php

namespace Luminary\Services\ApiRequest\Content;

class Related extends Content
{
    /**
     * The request body content
     *
     * @var array
     */
    protected $content;

    /**
     * The relationship type
     *
     * @var array
     */
    protected $type;

    /**
     * Content constructor.
     *
     * @param array $content
     * @param string $type
     */
    public function __construct(array $content, string $type)
    {
        parent::__construct($content);
        $this->type = $type;
    }

    /**
     * Set the document type from request
     *
     * @return string
     */
    public function type() :string
    {
        return $this->type;
    }
}

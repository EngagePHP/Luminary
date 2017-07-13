<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Luminary\Services\ApiResponse\Serializers\SerializerInterface;

class LinkPresenter
{
    /**
     * The serializer instance
     *
     * @var \Luminary\Services\ApiResponse\Serializers\SerializerInterface
     */
    protected $serializer;

    /**
     * PresenterInterface constructor
     *
     * @param \Luminary\Services\ApiResponse\Serializers\SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Return the formatted presenter array
     *
     * @return array
     */
    public function format() :array
    {
        return array_merge(
            $this->self(),
            $this->related(),
            $this->pagination()
        );
    }

    /**
     * Return teh self link as an array
     *
     * @return array
     */
    protected function self() :array
    {
        return [ 'self' =>  $this->serializer->selfLink()] ;
    }

    /**
     * return the related link as an array
     *
     * @return array
     */
    protected function related() :array
    {
        $link = $this->serializer->relatedLink();
        return empty($link) ? [] : ['related' => $link];
    }

    /**
     * Return the pagination links as an array
     *
     * @return array
     */
    protected function pagination()
    {
        $serializer = $this->serializer;

        if (! $serializer->paginated()) {
            return [];
        }

        return $serializer->paginationLinks();
    }
}

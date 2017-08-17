<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Luminary\Services\ApiResponse\ResponseHelper;
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
     * @param bool $query
     * @return array
     */
    public function format($query = false) :array
    {
        return array_merge(
            $this->self($query),
            $this->related(),
            $this->pagination()
        );
    }

    /**
     * Return teh self link as an array
     *
     * @param bool $addQuery
     * @return array
     */
    protected function self($addQuery = false) :array
    {
        $query = $addQuery ? $this->queryString() : '';
        return ['self' =>  $this->serializer->selfLink() . $query] ;
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
     * Get the current request query string
     *
     * @return string
     */
    protected function queryString()
    {
        $queryString = ResponseHelper::queryString();
        return $queryString ? '?' . $queryString : '';
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

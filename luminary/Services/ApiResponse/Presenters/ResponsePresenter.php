<?php

namespace Luminary\Services\ApiResponse\Presenters;

use Luminary\Services\ApiResponse\Serializers\SerializerInterface;

class ResponsePresenter
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
        $s = $this->serializer;
        $s->setTopLevel();

        return [
            'jsonapi' => [
                'version' => $s->jsonapi()
            ],
            'links' => $s->links(),
            'data' => $s->data(),
            'included' => $s->processIncluded(),
            'meta' => $s->responseMeta()
        ];
    }
}

<?php

namespace Luminary\Services\ApiRequest\Exceptions;

class UnacceptedAttribute extends MissingDataAttribute
{
    /**
     * The additional unaccepted attribute
     *
     * @var string
     */
    protected $attribute;

    /**
     * Constructor.
     *
     * @param string $attribute
     * @param string $message The internal exception message
     * @param \Exception $previous The previous exception
     * @param int $code The internal exception code
     */
    public function __construct(string $attribute, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->attribute = $attribute;

        parent::__construct($message, $previous, $code);
    }

    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data/'.$this->attribute
        ];
    }
}

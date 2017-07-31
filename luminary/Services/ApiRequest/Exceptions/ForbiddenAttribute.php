<?php

namespace Luminary\Services\ApiRequest\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ForbiddenAttribute extends HttpException
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
     * @param array $headers
     * @param int $code The internal exception code
     */
    public function __construct(
        string $attribute,
        $message = null,
        \Exception $previous = null,
        array $headers = [],
        $code = 0
    ) {
    
        $this->attribute = $attribute;

        parent::__construct(403, $message, $previous, $headers, $code);
    }

    /**
     * Set the response title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Forbidden Attribute';
    }

    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data/attributes/'.$this->attribute
        ];
    }
}

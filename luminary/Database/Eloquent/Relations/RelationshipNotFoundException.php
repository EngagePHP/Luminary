<?php

namespace Luminary\Database\Eloquent\Relations;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class RelationshipNotFoundException extends UnprocessableEntityHttpException
{
    /**
     * The relationship name
     *
     * @var string
     */
    public $relationship;

    /**
     * Exception Constructor
     *
     * @param string $relationship
     * @param string $message
     * @param \Exception $previous
     * @param int $code
     */
    public function __construct($relationship, $message = null, \Exception $previous = null, $code = 0)
    {
        $this->relationship = $relationship;

        parent::__construct($message, $previous, $code);
    }

    /**
     * Set the response title
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Bad Request';
    }

    /**
     * Set the response pointer source
     *
     * @return array
     */
    public function getSource()
    {
        return [
            'pointer' => '/data/relationships/'.$this->relationship
        ];
    }
}

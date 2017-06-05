<?php

namespace Luminary\Services\Filesystem\S3;

use Closure;
use EddTurtle\DirectUpload\Signature as DirectUploadSignature;

trait PolicyGenerator
{
    /**
     * Override parent policy generator to include
     * server side encryption policy condition
     *
     * @return void
     */
    protected function generatePolicy()
    {
        parent::generatePolicy();

        $options = $this->get('options');
        $encrypted = array_get($options, 'encryption');

        // Return if encrypted option false
        if (! $encrypted) {
            return;
        }

        // Decode the policy
        $policy = $this->get('base64Policy');
        $policy = json_decode(base64_decode($policy), true);

        // Add the encryption condition to policy
        $policy['conditions'][] = ['x-amz-server-side-encryption' => 'AES256'];

        // Encrypt & set the policy
        $policy = base64_encode(json_encode($policy));
        $this->set('base64Policy', $policy);
    }

    /**
     * Get a private property from the parent class
     *
     * @param $property
     * @return mixed
     */
    private function get($property)
    {
        $closure = $this->bind(function ($property) {
            return $this->{$property};
        });

        return $closure($property);
    }

    /**
     * Set a private property in the parent class
     *
     * @param $property
     * @param $value
     * @return mixed
     */
    private function set($property, $value)
    {
        $closure = $this->bind(function ($property, $value) {
            $this->{$property} = $value;
        });

        return $closure($property, $value);
    }

    /**
     * Closure to bind/set/get overrides to parent class
     *
     * @param Closure $closure
     * @return Closure
     */
    private function bind(Closure $closure)
    {
        return Closure::bind($closure, $this, "\\" . DirectUploadSignature::class);
    }
}

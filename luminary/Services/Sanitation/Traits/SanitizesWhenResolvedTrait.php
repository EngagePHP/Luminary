<?php

namespace Luminary\Services\Sanitation\Traits;

use Luminary\Services\Sanitation\Sanitizers\Input;

trait SanitizesWhenResolvedTrait
{
    /**
     * Sanitize the request
     *
     * @return void
     */
    public function sanitizeInstance()
    {
        $this->prepareForSanitation();

        if (! method_exists($this, 'sanitize')) {
            return;
        }

        $input = $this->all();
        $attributes = $this->sanitize();

        $sanitized = $this->sanitizeInput($attributes, $input);

        $this->merge($sanitized);
    }

    /**
     * Sanitize the request input
     *
     * @param array $attributes
     * @param array $input
     * @return array
     */
    protected function sanitizeInput(array $attributes, array $input)
    {
        $sanitize = array_intersect_key($input, $attributes);

        foreach ($sanitize as $key => &$item) {
            $item = is_array($attributes[$key])
                ? $this->sanitizeInput($attributes[$key], $item)
                : Input::sanitize($item, $attributes[$key]);
        }

        return $sanitize;
    }

    /**
     * Prepare the data for sanitation.
     *
     * @return void
     */
    protected function prepareForSanitation()
    {
        // no default action
    }
}

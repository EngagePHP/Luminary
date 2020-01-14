<?php

namespace Luminary\Exceptions\Presenters;

class ValidationPresenter extends DefaultPresenter
{
    /**
     * Return the error response array
     *
     * @return array
     */
    public function response() :array
    {
        $response = [];

        collect($this->exception->errors())->each(
            function($messages, $field) use(&$response) {
                $generated = $this->generateResponseErrorItems((string) $field, $messages);
                $response = array_merge($response, $generated);
            }
        );

        return $response;
    }

    /**
     * Generate the message array of error items
     *
     * @param string $field
     * @param array $messages
     * @return array
     */
    protected function generateResponseErrorItems(string $field, array $messages)
    {
        return collect($messages)->map(
            function($message) use($field) {
                return $this->generateResponseErrorItem($field, $message);
            }
        )->toArray();
    }

    /**
     * Generate the response array for an error item
     *
     * @param string $field
     * @param string $message
     * @return array
     */
    protected function generateResponseErrorItem(string $field, string $message)
    {
        return [
            'code' => '',
            'source' => $this->itemSource($field),
            'title' => $this->itemTitle($message),
            'detail' => $this->itemDetail($message)
        ];
    }

    /**
     * Get the formatted item source
     *
     * @param string $field
     * @return array
     */
    public function itemSource(string $field)
    {
        return [
            'pointer' => 'data/attributes/' . $field
        ];
    }

    /**
     * Get the error title
     *
     * @param string $message
     * @return mixed
     */
    public function itemTitle(string $message)
    {
        $parts = explode('|', $message);
        return array_first($parts);
    }

    /**
     * Get the error description
     *
     * @param string $message
     * @return string
     */
    public function itemDetail(string $message)
    {
        $parts = explode('|', $message);
        array_shift($parts);

        return implode('.', $parts);
    }
}

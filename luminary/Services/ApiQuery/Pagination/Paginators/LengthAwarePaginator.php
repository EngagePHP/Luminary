<?php

namespace Luminary\Services\ApiQuery\Pagination\Paginators;

use Illuminate\Pagination\LengthAwarePaginator as IlluminateLengthAwarePaginator;
use Illuminate\Support\Str;

class LengthAwarePaginator extends IlluminateLengthAwarePaginator
{
    /**
     * Get the URL for a given page number.
     *
     * @param  int  $page
     * @return string
     */
    public function url($page)
    {
        if ($page <= 0) {
            $page = 1;
        }

        // If we have any extra query string key / value pairs that need to be added
        // onto the URL, we will put them in query string form and then attach it
        // to the URL. This allows for extra information like sortings storage.
        $parameters = $this->createUrlParameters($page, $this->perPage());

        if (count($this->query) > 0) {
            $parameters = array_merge($this->query, $parameters);
        }

        return $this->path
        .(Str::contains($this->path, '?') ? '&' : '?')
        .http_build_query($parameters, '', '&')
        .$this->buildFragment();
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'current_page' => $this->currentPage(),
            'first_page_url' => $this->firstPageUrl(),
            'from' => $this->firstItem(),
            'last_page' => $this->lastPage(),
            'last_page_url' => $this->lastPageUrl(),
            'next_page_url' => $this->nextPageUrl(),
            'path' => $this->path,
            'per_page' => $this->perPage(),
            'prev_page_url' => $this->previousPageUrl(),
            'to' => $this->lastItem(),
            'total' => $this->total(),
        ];
    }

    /**
     * Get the URL for the first page.
     *
     * @return string
     */
    public function firstPageUrl() :string
    {
        return $this->url(1);
    }

    /**
     * Get the URL for the last page.
     *
     * @return string
     */
    public function lastPageUrl() :string
    {
        return $this->url($this->lastPage());
    }

    /**
     * Create the url query array
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function createUrlParameters(int $page, int $perPage) :array
    {
        return [
            'page' => [
                'number' => $page,
                'size' => $perPage
            ]
        ];
    }
}

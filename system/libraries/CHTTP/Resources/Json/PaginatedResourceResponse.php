<?php

class CHTTP_Resources_Json_PaginatedResourceResponse extends CHTTP_Resources_Json_ResourceResponse {
    /**
     * Create an HTTP response that represents the object.
     *
     * @param \CHTTP_Request $request
     *
     * @return \CHTTP_JsonResponse
     */
    public function toResponse($request) {
        return c::tap(c::response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                array_merge_recursive(
                    $this->paginationInformation($request),
                    $this->resource->with($request),
                    $this->resource->additional
                )
            ),
            $this->calculateStatus(),
            [],
            $this->resource->jsonOptions()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource->map(function ($item) {
                return is_array($item) ? carr::get($item, 'resource') : c::optional($item)->resource;
            });

            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Add the pagination information to the response.
     *
     * @param \CHTTP_Request $request
     *
     * @return array
     */
    protected function paginationInformation($request) {
        $paginated = $this->resource->resource->toArray();

        $default = [
            'links' => $this->paginationLinks($paginated),
            'meta' => $this->meta($paginated),
        ];

        if (method_exists($this->resource, 'paginationInformation')
            || $this->resource->hasMacro('paginationInformation')
        ) {
            return $this->resource->paginationInformation($request, $paginated, $default);
        }

        return $default;
    }

    /**
     * Get the pagination links for the response.
     *
     * @param array $paginated
     *
     * @return array
     */
    protected function paginationLinks($paginated) {
        return [
            'first' => $paginated['first_page_url'] ?? null,
            'last' => $paginated['last_page_url'] ?? null,
            'prev' => $paginated['prev_page_url'] ?? null,
            'next' => $paginated['next_page_url'] ?? null,
        ];
    }

    /**
     * Gather the meta data for the response.
     *
     * @param array $paginated
     *
     * @return array
     */
    protected function meta($paginated) {
        return carr::except($paginated, [
            'data',
            'first_page_url',
            'last_page_url',
            'prev_page_url',
            'next_page_url',
        ]);
    }
}

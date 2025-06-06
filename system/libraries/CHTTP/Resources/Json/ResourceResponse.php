<?php

class CHTTP_Resources_Json_ResourceResponse implements CInterface_Responsable {
    /**
     * The underlying resource.
     *
     * @var mixed
     */
    public $resource;

    /**
     * Create a new resource response.
     *
     * @param mixed $resource
     */
    public function __construct($resource) {
        $this->resource = $resource;
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @paramCHTTP_Request $request
     *
     * @param mixed $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request) {
        return c::tap(c::response()->json(
            $this->wrap(
                $this->resource->resolve($request),
                $this->resource->with($request),
                $this->resource->additional
            ),
            $this->calculateStatus(),
            [],
            $this->resource->jsonOptions()
        ), function ($response) use ($request) {
            $response->original = $this->resource->resource;

            $this->resource->withResponse($request, $response);
        });
    }

    /**
     * Wrap the given data if necessary.
     *
     * @param \CCollection|array $data
     * @param array              $with
     * @param array              $additional
     *
     * @return array
     */
    protected function wrap($data, $with = [], $additional = []) {
        if ($data instanceof CCollection) {
            $data = $data->all();
        }

        if ($this->haveDefaultWrapperAndDataIsUnwrapped($data)) {
            $data = [$this->wrapper() => $data];
        } elseif ($this->haveAdditionalInformationAndDataIsUnwrapped($data, $with, $additional)) {
            $data = [($this->wrapper() ?? 'data') => $data];
        }

        return array_merge_recursive($data, $with, $additional);
    }

    /**
     * Determine if we have a default wrapper and the given data is unwrapped.
     *
     * @param array $data
     *
     * @return bool
     */
    protected function haveDefaultWrapperAndDataIsUnwrapped($data) {
        return $this->wrapper() && !array_key_exists($this->wrapper(), $data);
    }

    /**
     * Determine if "with" data has been added and our data is unwrapped.
     *
     * @param array $data
     * @param array $with
     * @param array $additional
     *
     * @return bool
     */
    protected function haveAdditionalInformationAndDataIsUnwrapped($data, $with, $additional) {
        return (!empty($with) || !empty($additional))
               && (!$this->wrapper()
                || !array_key_exists($this->wrapper(), $data));
    }

    /**
     * Get the default data wrapper for the resource.
     *
     * @return string
     */
    protected function wrapper() {
        return get_class($this->resource)::$wrap;
    }

    /**
     * Calculate the appropriate status code for the response.
     *
     * @return int
     */
    protected function calculateStatus() {
        return $this->resource->resource instanceof CModel
               && $this->resource->resource->wasRecentlyCreated ? 201 : 200;
    }
}

<?php

use League\Fractal\TransformerAbstract;
use League\Fractal\Manager as FractalManager;
use League\Fractal\Resource\Item as FractalItem;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection as FractalCollection;

class CApi_Transformer_Adapter_FractalAdapter implements CApi_Contract_Transformer_AdapterInterface {
    /**
     * Fractal manager instance.
     *
     * @var \League\Fractal\Manager
     */
    protected $fractal;

    /**
     * The include query string key.
     *
     * @var string
     */
    protected $includeKey;

    /**
     * The include separator.
     *
     * @var string
     */
    protected $includeSeparator;

    /**
     * Indicates if eager loading is enabled.
     *
     * @var bool
     */
    protected $eagerLoading = true;

    /**
     * Create a new fractal transformer instance.
     *
     * @param \League\Fractal\Manager $fractal
     * @param string                  $includeKey
     * @param string                  $includeSeparator
     * @param bool                    $eagerLoading
     *
     * @return void
     */
    public function __construct(FractalManager $fractal, $includeKey = 'include', $includeSeparator = ',', $eagerLoading = true) {
        $this->fractal = $fractal;
        $this->includeKey = $includeKey;
        $this->includeSeparator = $includeSeparator;
        $this->eagerLoading = $eagerLoading;
    }

    /**
     * Transform a response with a transformer.
     *
     * @param mixed                                     $response
     * @param League\Fractal\TransformerAbstract|object $transformer
     * @param \CApi_Transformer_Binding                 $binding
     * @param \CApi_HTTP_Request                        $request
     *
     * @return array
     */
    public function transform($response, $transformer, CApi_Transformer_Binding $binding, CApi_HTTP_Request $request) {
        $this->parseFractalIncludes($request);

        $resource = $this->createResource($response, $transformer, $parameters = $binding->getParameters());

        // If the response is a paginator then we'll create a new paginator
        // adapter for Laravel and set the paginator instance on our
        // collection resource.
        if ($response instanceof CPagination_PaginatorInterface) {
            $paginator = $this->createPaginatorAdapter($response);

            $resource->setPaginator($paginator);
        }

        if ($this->shouldEagerLoad($response)) {
            $eagerLoads = $this->mergeEagerLoads($transformer, $this->fractal->getRequestedIncludes());

            if ($transformer instanceof TransformerAbstract) {
                // Only eager load the items in available includes
                $eagerLoads = array_intersect($eagerLoads, $transformer->getAvailableIncludes());
            }

            $response->load($eagerLoads);
        }

        foreach ($binding->getMeta() as $key => $value) {
            $resource->setMetaValue($key, $value);
        }

        $binding->fireCallback($resource, $this->fractal);

        $identifier = isset($parameters['identifier']) ? $parameters['identifier'] : null;

        return $this->fractal->createData($resource, $identifier)->toArray();
    }

    /**
     * Eager loading is only performed when the response is or contains an
     * Eloquent collection and eager loading is enabled.
     *
     * @param mixed $response
     *
     * @return bool
     */
    protected function shouldEagerLoad($response) {
        if ($response instanceof CPagination_PaginatorInterface) {
            /** @var CPagination_AbstractPaginator $response */
            $response = $response->getCollection();
        }

        return $response instanceof CModel_Collection && $this->eagerLoading;
    }

    /**
     * Create the Fractal paginator adapter.
     *
     * @param \CPagination_PaginatorInterface $paginator
     *
     * @return \CApi_Transformer_Adapter_Fractal_PaginatorAdapter
     */
    protected function createPaginatorAdapter(CPagination_PaginatorInterface $paginator) {
        return new CApi_Transformer_Adapter_Fractal_PaginatorAdapter($paginator);
    }

    /**
     * Create a Fractal resource instance.
     *
     * @param mixed                               $response
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param array                               $parameters
     *
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\Collection
     */
    protected function createResource($response, $transformer, array $parameters) {
        $key = isset($parameters['key']) ? $parameters['key'] : null;

        if ($response instanceof CPagination_PaginatorInterface || $response instanceof CCollection) {
            return new FractalCollection($response, $transformer, $key);
        }

        return new FractalItem($response, $transformer, $key);
    }

    /**
     * Parse the includes.
     *
     * @param \CApi_HTTP_Request $request
     *
     * @return void
     */
    public function parseFractalIncludes(CApi_HTTP_Request $request) {
        $includes = $request->input($this->includeKey);

        if (!is_array($includes)) {
            $includes = array_map('trim', array_filter(explode($this->includeSeparator, $includes)));
        }

        $this->fractal->parseIncludes($includes);
    }

    /**
     * Get the underlying Fractal instance.
     *
     * @return \League\Fractal\Manager
     */
    public function getFractal() {
        return $this->fractal;
    }

    /**
     * Get includes as their array keys for eager loading.
     *
     * @param \League\Fractal\TransformerAbstract $transformer
     * @param string|array                        $requestedIncludes
     *
     * @return array
     */
    protected function mergeEagerLoads($transformer, $requestedIncludes) {
        $includes = array_merge($requestedIncludes, $transformer->getDefaultIncludes());

        $eagerLoads = [];

        foreach ($includes as $key => $value) {
            $eagerLoads[] = is_string($key) ? $key : $value;
        }

        $lazyLoadedIncludes = 'lazyLoadedIncludes';
        if (property_exists($transformer, $lazyLoadedIncludes)) {
            $eagerLoads = array_diff($eagerLoads, $transformer->$lazyLoadedIncludes);
        }

        return $eagerLoads;
    }

    /**
     * Disable eager loading.
     *
     * @return \CApi_Transformer_Adapter_FractalAdapter
     */
    public function disableEagerLoading() {
        $this->eagerLoading = false;

        return $this;
    }

    /**
     * Enable eager loading.
     *
     * @return \CApi_Transformer_Adapter_FractalAdapter
     */
    public function enableEagerLoading() {
        $this->eagerLoading = true;

        return $this;
    }
}

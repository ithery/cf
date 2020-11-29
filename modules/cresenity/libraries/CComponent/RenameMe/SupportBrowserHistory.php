<?php





use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class CComponent_RenameMe_SupportBrowserHistory
{
    static function init() { return new static; }

    protected $mergedQueryParamsFromDehydratedComponents;

    function __construct()
    {
        $this->mergedQueryParamsFromDehydratedComponents = c::collect($this->getExistingQueryParams());

        CComponent_Manager::instance()->listen('component.hydrate.initial', function ($component) {
            if (! $properties = $this->getQueryParamsFromComponentProperties($component)->keys()) return;

            $queryParams = CHTTP::request()->query();

            foreach ($properties as $property) {
                $fromQueryString = carr::get($queryParams, $property);

                $decoded = is_array($fromQueryString)
                    ? json_decode(json_encode($fromQueryString), true)
                    : json_decode($fromQueryString, true);

                if ($fromQueryString !== null) {
                    $component->$property = $decoded === null ? $fromQueryString : $decoded;
                }
            }
        });

        CComponent_Manager::instance()->listen('component.dehydrate.initial', function (CComponent $component, CComponent_Response $response) {
            if (! $this->shouldSendPath($component)) return;

            $queryParams = $this->mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest($component);
            
            $response->effects['path'] = curl::fullUrl(false).$this->stringifyQueryParams($queryParams);
        });

        CComponent_Manager::instance()->listen('component.dehydrate.subsequent', function (CComponent $component, CComponent_Response $response) {
            if (! $referer = CHTTP::request()->header('Referer')) return;

            $route = $this->getRouteFromReferer($referer);

            if ( ! $this->shouldSendPath($component, $route)) return;

            $queryParams = $this->mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest($component);

            if ($route && false !== strpos($route->getActionName(), get_class($component))) {
                $path = $response->effects['path'] = $this->buildPathFromRoute($component, $route, $queryParams);
            } else {
                $path = $this->buildPathFromReferer($referer, $queryParams);
            }

            if ($referer !== $path) {
                $response->effects['path'] = $path;
            }
        });
    }

    protected function getRouteFromReferer($referer)
    {
        try {
            /*
            // See if we can get the route from the referer.
            return app('router')->getRoutes()->match(
                CHTTP::request()->create($referer, 'GET')
            );
             * 
             */
        } catch (NotFoundHttpException $e) {
            // If not, use the current route.
            return app('router')->current();
        }
    }

    protected function shouldSendPath($component, $route = null)
    {
        // If the component is setting $queryString params.
        if (! $this->getQueryParamsFromComponentProperties($component)->isEmpty()) return true;
        /*
        $route = $route ?$route :curl::fullUrl(false);

        if (
            $route
            && is_string($action = $route->getActionName())
            // If the component is registered using `Route::get()`.
            && c::str($action)->contains(get_class($component))
            // AND, the component is tracking route params as its public properties
            && count(array_intersect_key($component->getPublicPropertiesDefinedBySubClass(), $route->parametersWithoutNulls()))
        ) {
            return true;
        }
         * s
         */

        return false;
    }

    protected function getExistingQueryParams()
    {
        return CComponent_Manager::instance()->isLivewireRequest()
            ? $this->getQueryParamsFromRefererHeader()
            : CHTTP::request()->query();
    }

    public function getQueryParamsFromRefererHeader()
    {
        if (empty($referer = CHTTP::request()->header('Referer'))) return [];

        parse_str(parse_url($referer, PHP_URL_QUERY), $refererQueryString);

        return $refererQueryString;
    }

    protected function buildPathFromReferer($referer, $queryParams) 
    {
        return c::str($referer)->before('?').$this->stringifyQueryParams($queryParams);
    }

    protected function buildPathFromRoute($component, $route, $queryString)
    {
        $boundParameters = array_merge(
            $route->parametersWithoutNulls(),
            array_intersect_key(
                $component->getPublicPropertiesDefinedBySubClass(),
                $route->parametersWithoutNulls()
            )
        );

        return CRouting::urlGenerator()->toRoute($route, $boundParameters + $queryString->toArray(), true);
    }

    protected function mergeComponentPropertiesWithExistingQueryParamsFromOtherComponentsAndTheRequest($component)
    {
        $excepts = $this->getExceptsFromComponent($component);

        $this->mergedQueryParamsFromDehydratedComponents = c::collect(CHTTP::request()->query())
            ->merge($this->mergedQueryParamsFromDehydratedComponents)
            ->merge($this->getQueryParamsFromComponentProperties($component))
            ->reject(function ($value, $key) use ($excepts) {
                return isset($excepts[$key]) && $excepts[$key] === $value;
            })
            ->map(function ($property) {
                return is_bool($property) ? json_encode($property) : $property;
            });

        return $this->mergedQueryParamsFromDehydratedComponents;
    }

    protected function getExceptsFromComponent($component)
    {
        return c::collect($component->getQueryString())
            ->filter(function ($value) {
                return isset($value['except']);
            })
            ->mapWithKeys(function ($value, $key) {
                return [$key => $value['except']];
            });
    }

    protected function getQueryParamsFromComponentProperties($component)
    {
        return c::collect($component->getQueryString())
            ->mapWithKeys(function($value, $key) use ($component) {
                $key = is_string($key) ? $key : $value;

                return [$key => $component->{$key}];
            });
    }

    protected function stringifyQueryParams($queryParams)
    {
        return $queryParams->isEmpty() ? '' : '?'.http_build_query($queryParams->toArray());
    }
}

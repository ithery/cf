<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Unit tests for CRouting_RouteCollection (and the storage/lookup behavior
 * it inherits from CRouting_RouteCollectionAbstract).
 *
 * This is this framework's independent reimplementation of
 * Illuminate\Routing\RouteCollection.
 */
class RouteCollectionTest extends TestCase {
    /**
     * @var CHTTP_Request
     */
    protected $originalUrlRequest;

    protected function setUp(): void {
        parent::setUp();
        // RouteCollection::match() falls back to CRouting_RouteFinder::find() when no
        // registered route matches. Under CF::isTesting() (set via phpunit.xml's
        // APP_ENV=testing server var) that fallback resolves the URI from
        // c::url()->getRequest()->path() -- the app's globally bound "current request"
        // singleton -- NOT from the $request passed into match(). To keep the
        // no-match/method-mismatch tests deterministic we bind that singleton to the
        // same request under test before calling match(), and restore it afterwards.
        $this->originalUrlRequest = c::url()->getRequest();
    }

    protected function tearDown(): void {
        c::url()->setRequest($this->originalUrlRequest);
        parent::tearDown();
    }

    protected function makeRoute($methods, $uri, $action = null) {
        if ($action === null) {
            $action = ['uses' => function () {
                return 'ok';
            }];
        }

        return new CRouting_Route($methods, $uri, $action);
    }

    /**
     * Match a request against a collection, first binding it as the app's
     * "current request" so CRouting_RouteFinder's fallback lookup (used when no
     * route matches directly) is resolved consistently with the request under test.
     *
     * @param CRouting_RouteCollection $collection
     * @param CHTTP_Request            $request
     *
     * @return CRouting_Route
     */
    protected function matchRequest($collection, $request) {
        c::url()->setRequest($request);

        return $collection->match($request);
    }

    // ------------------------------------------------------------------
    // add() / getRoutes() / count() / iteration
    // ------------------------------------------------------------------

    public function testAddReturnsTheRoute() {
        $collection = new CRouting_RouteCollection();
        $route = $this->makeRoute('GET', 'foo');

        $this->assertSame($route, $collection->add($route));
    }

    public function testCountReflectsNumberOfDistinctRoutes() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));
        $collection->add($this->makeRoute('POST', 'bar'));

        $this->assertCount(2, $collection);
        $this->assertSame(2, count($collection));
    }

    public function testGetRoutesReturnsAllAddedRoutes() {
        $collection = new CRouting_RouteCollection();
        $r1 = $collection->add($this->makeRoute('GET', 'foo'));
        $r2 = $collection->add($this->makeRoute('POST', 'bar'));

        $this->assertSame([$r1, $r2], $collection->getRoutes());
    }

    public function testCollectionIsIterable() {
        $collection = new CRouting_RouteCollection();
        $r1 = $collection->add($this->makeRoute('GET', 'foo'));

        $found = [];
        foreach ($collection as $route) {
            $found[] = $route;
        }

        $this->assertSame([$r1], $found);
    }

    public function testAddingSameMethodAndUriOverwritesPreviousRoute() {
        $collection = new CRouting_RouteCollection();
        $first = $collection->add($this->makeRoute('GET', 'foo'));
        $second = $collection->add($this->makeRoute('GET', 'foo'));

        $this->assertCount(1, $collection);
        $this->assertSame([$second], $collection->getRoutes());
        $this->assertNotSame($first, $second);
    }

    public function testDistinctUrisWithSameMethodAreBothKept() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));
        $collection->add($this->makeRoute('GET', 'bar'));

        $this->assertCount(2, $collection);
    }

    // ------------------------------------------------------------------
    // get() by method
    // ------------------------------------------------------------------

    public function testGetByMethodReturnsOnlyMatchingRoutes() {
        $collection = new CRouting_RouteCollection();
        $get = $collection->add($this->makeRoute('GET', 'foo'));
        $collection->add($this->makeRoute('POST', 'bar'));

        $getRoutes = $collection->get('GET');

        $this->assertCount(1, $getRoutes);
        $this->assertSame($get, reset($getRoutes));
    }

    public function testGetWithUnknownMethodReturnsEmptyArray() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));

        $this->assertSame([], $collection->get('DELETE'));
    }

    public function testGetWithNoArgumentReturnsAllRoutes() {
        $collection = new CRouting_RouteCollection();
        $r1 = $collection->add($this->makeRoute('GET', 'foo'));
        $r2 = $collection->add($this->makeRoute('POST', 'bar'));

        $this->assertSame([$r1, $r2], $collection->get());
    }

    public function testGetRoutesByMethodGroupsRoutesUnderEachVerb() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));

        $byMethod = $collection->getRoutesByMethod();

        $this->assertArrayHasKey('GET', $byMethod);
        $this->assertArrayHasKey('HEAD', $byMethod);
    }

    // ------------------------------------------------------------------
    // Name lookups
    // ------------------------------------------------------------------

    public function testGetByNameFindsNamedRoute() {
        $collection = new CRouting_RouteCollection();
        $route = $this->makeRoute('GET', 'foo')->name('foo.show');
        $collection->add($route);

        $this->assertSame($route, $collection->getByName('foo.show'));
    }

    public function testGetByNameReturnsNullWhenNotFound() {
        $collection = new CRouting_RouteCollection();

        $this->assertNull($collection->getByName('missing'));
    }

    public function testHasNamedRoute() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo')->name('foo.show'));

        $this->assertTrue($collection->hasNamedRoute('foo.show'));
        $this->assertFalse($collection->hasNamedRoute('nope'));
    }

    public function testGetRoutesByNameReturnsNameLookupTable() {
        $collection = new CRouting_RouteCollection();
        $route = $this->makeRoute('GET', 'foo')->name('foo.show');
        $collection->add($route);

        $this->assertSame(['foo.show' => $route], $collection->getRoutesByName());
    }

    public function testRouteAddedUnnamedIsNotInNameLookup() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));

        $this->assertSame([], $collection->getRoutesByName());
    }

    public function testRefreshNameLookupsPicksUpNameAssignedAfterAdd() {
        $collection = new CRouting_RouteCollection();
        $route = $this->makeRoute('GET', 'foo');
        $collection->add($route);

        $this->assertFalse($collection->hasNamedRoute('foo.late'));

        // Name the route fluently, after it has already been added.
        $route->name('foo.late');
        $this->assertFalse($collection->hasNamedRoute('foo.late'));

        $collection->refreshNameLookups();
        $this->assertTrue($collection->hasNamedRoute('foo.late'));
        $this->assertSame($route, $collection->getByName('foo.late'));
    }

    // ------------------------------------------------------------------
    // Action lookups
    // ------------------------------------------------------------------

    public function testGetByActionFindsRouteByControllerAction() {
        $collection = new CRouting_RouteCollection();
        $action = 'RCTDummyController@index';
        $route = $this->makeRoute('GET', 'foo', ['uses' => $action, 'controller' => $action]);
        $collection->add($route);

        $this->assertSame($route, $collection->getByAction($action));
    }

    public function testGetByActionReturnsNullWhenNotFound() {
        $collection = new CRouting_RouteCollection();

        $this->assertNull($collection->getByAction('Nope@nope'));
    }

    public function testGetByActionTrimsLeadingBackslash() {
        $collection = new CRouting_RouteCollection();
        $action = '\\RCTDummyController@index';
        $route = $this->makeRoute('GET', 'foo', ['uses' => $action, 'controller' => $action]);
        $collection->add($route);

        $this->assertSame($route, $collection->getByAction('RCTDummyController@index'));
    }

    public function testClosureActionIsNotAddedToActionLookup() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'foo'));

        $this->assertNull($collection->getByAction('Closure'));
    }

    public function testRefreshActionLookupsPicksUpActionAssignedAfterAdd() {
        $collection = new CRouting_RouteCollection();
        $route = $this->makeRoute('GET', 'foo');
        $collection->add($route);

        // uses() resolves group namespacing through the attached router instance,
        // so it needs one even though we never use groups here.
        $route->setRouter(new CRouting_Router());
        $action = 'RCTDummyController@late';
        $route->uses(['RCTDummyController', 'late']);

        $this->assertNull($collection->getByAction($action));

        $collection->refreshActionLookups();
        $this->assertSame($route, $collection->getByAction($action));
    }

    // ------------------------------------------------------------------
    // match()
    // ------------------------------------------------------------------

    public function testMatchFindsRouteForMethodAndUri() {
        $collection = new CRouting_RouteCollection();
        $route = $collection->add($this->makeRoute('GET', 'users'));

        $matched = $this->matchRequest($collection, CHTTP_Request::create('/users', 'GET'));

        $this->assertSame($route, $matched);
    }

    public function testMatchBindsParametersOnTheReturnedRoute() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'users/{id}'));

        $matched = $this->matchRequest($collection, CHTTP_Request::create('/users/77', 'GET'));

        $this->assertSame(['id' => '77'], $matched->parameters());
    }

    public function testMatchDistinguishesRoutesByMethod() {
        $collection = new CRouting_RouteCollection();
        $getRoute = $collection->add($this->makeRoute('GET', 'users'));
        $postRoute = $collection->add($this->makeRoute('POST', 'users'));

        $this->assertSame($getRoute, $this->matchRequest($collection, CHTTP_Request::create('/users', 'GET')));
        $this->assertSame($postRoute, $this->matchRequest($collection, CHTTP_Request::create('/users', 'POST')));
    }

    public function testMatchThrowsNotFoundForUnmatchedUri() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'users'));

        $this->expectException(NotFoundHttpException::class);
        // A nonsense URI that also cannot resolve to any real controller class,
        // so the RouteCollection's fallback-to-controller-routing lookup misses too.
        $this->matchRequest($collection, CHTTP_Request::create('/zzz_rct_nonexistent_path/zzz_method', 'GET'));
    }

    public function testMatchThrowsMethodNotAllowedWhenOnlyOtherVerbsMatch() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'users'));

        $this->expectException(MethodNotAllowedHttpException::class);
        $this->matchRequest($collection, CHTTP_Request::create('/users', 'DELETE'));
    }

    public function testMethodNotAllowedExceptionListsAllowedMethods() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'users'));

        try {
            $this->matchRequest($collection, CHTTP_Request::create('/users', 'DELETE'));
            $this->fail('Expected MethodNotAllowedHttpException was not thrown.');
        } catch (MethodNotAllowedHttpException $e) {
            $this->assertSame(['GET', 'HEAD'], $e->getHeaders()['Allow'] === null ? null : explode(', ', $e->getHeaders()['Allow']));
        }
    }

    public function testMatchOptionsRequestReturnsAllowHeaderRoute() {
        $collection = new CRouting_RouteCollection();
        $collection->add($this->makeRoute('GET', 'users'));

        $route = $this->matchRequest($collection, CHTTP_Request::create('/users', 'OPTIONS'));

        $response = $route->run();
        $this->assertSame('GET,HEAD', $response->headers->get('Allow'));
    }

    public function testMatchPrefersNonFallbackRouteOverFallback() {
        $collection = new CRouting_RouteCollection();
        $fallback = $this->makeRoute('GET', '{any}', ['uses' => function () {
            return 'fallback';
        }])->fallback();
        $specific = $this->makeRoute('GET', 'users', ['uses' => function () {
            return 'specific';
        }]);

        $collection->add($fallback);
        $collection->add($specific);

        $matched = $this->matchRequest($collection, CHTTP_Request::create('/users', 'GET'));

        $this->assertSame($specific, $matched);
    }

    public function testMatchFallsBackWhenNoSpecificRouteMatches() {
        $collection = new CRouting_RouteCollection();
        $fallback = $this->makeRoute('GET', '{any}', ['uses' => function () {
            return 'fallback';
        }])->fallback();
        $specific = $this->makeRoute('GET', 'users', ['uses' => function () {
            return 'specific';
        }]);

        $collection->add($fallback);
        $collection->add($specific);

        $matched = $this->matchRequest($collection, CHTTP_Request::create('/anything-else', 'GET'));

        $this->assertSame($fallback, $matched);
    }
}

class RCTDummyController {
    public function index() {
        return 'index';
    }

    public function late() {
        return 'late';
    }
}

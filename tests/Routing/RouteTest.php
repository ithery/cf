<?php

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for CRouting_Route.
 *
 * CRouting_Route is this framework's independent reimplementation of
 * Illuminate\Routing\Route. It is constructed directly (no booted app
 * needed) as new CRouting_Route($methods, $uri, $action) and is bound
 * to a CHTTP_Request (itself a thin extension of Symfony's Request,
 * built via CHTTP_Request::create($uri, $method)) to extract parameters.
 */
class RouteTest extends TestCase {
    protected function makeRoute($methods, $uri, $action = null) {
        if ($action === null) {
            $action = ['uses' => function () {
                return 'hello';
            }];
        }

        return new CRouting_Route($methods, $uri, $action);
    }

    // ------------------------------------------------------------------
    // Construction / basic accessors
    // ------------------------------------------------------------------

    public function testBasicDispatchingOfRoutes() {
        $route = $this->makeRoute('GET', 'foo/bar');

        $this->assertSame(['GET', 'HEAD'], $route->methods());
        $this->assertSame('foo/bar', $route->uri());
    }

    public function testGetAddsHeadAutomatically() {
        $route = $this->makeRoute(['GET'], 'foo');

        $this->assertContains('HEAD', $route->methods());
    }

    public function testPostDoesNotAddHead() {
        $route = $this->makeRoute(['POST'], 'foo');

        $this->assertNotContains('HEAD', $route->methods());
    }

    public function testMultipleMethodsCanBeAssigned() {
        $route = $this->makeRoute(['GET', 'POST'], 'foo');

        $this->assertSame(['GET', 'POST', 'HEAD'], $route->methods());
    }

    public function testUriAccessorsAndArrayActionAreParsed() {
        $route = $this->makeRoute('GET', 'foo/bar', ['uses' => function () {
            return 'baz';
        }]);

        $this->assertSame('foo/bar', $route->uri());
        $this->assertSame('foo/bar', $route->uri);
    }

    // ------------------------------------------------------------------
    // Naming
    // ------------------------------------------------------------------

    public function testRouteCanBeNamedAndRetrieved() {
        $route = $this->makeRoute('GET', 'foo');
        $this->assertNull($route->getName());

        $route->name('foo.index');

        $this->assertSame('foo.index', $route->getName());
    }

    public function testNameIsAppendedNotReplacedOnSubsequentCalls() {
        $route = $this->makeRoute('GET', 'foo');

        $route->name('foo.');
        $route->name('index');

        $this->assertSame('foo.index', $route->getName());
    }

    public function testNamedMatchesGivenPatterns() {
        $route = $this->makeRoute('GET', 'foo')->name('foo.index');

        $this->assertTrue($route->named('foo.index'));
        $this->assertTrue($route->named('foo.*'));
        $this->assertFalse($route->named('bar.*'));
    }

    public function testNamedReturnsFalseWhenRouteHasNoName() {
        $route = $this->makeRoute('GET', 'foo');

        $this->assertFalse($route->named('anything'));
    }

    // ------------------------------------------------------------------
    // where() constraints + matches()
    // ------------------------------------------------------------------

    public function testRouteMatchesWithoutConstraint() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $request = CHTTP_Request::create('/user/123', 'GET');

        $this->assertTrue($route->matches($request));
    }

    public function testWhereConstraintRejectsNonMatchingSegment() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->where('id', '[0-9]+');

        $numeric = CHTTP_Request::create('/user/123', 'GET');
        $alpha = CHTTP_Request::create('/user/abc', 'GET');

        $this->assertTrue($route->matches($numeric));
        $this->assertFalse($route->matches($alpha));
    }

    public function testWhereAcceptsArrayOfConstraints() {
        $route = $this->makeRoute('GET', 'user/{id}/{name}');
        $route->where(['id' => '[0-9]+', 'name' => '[a-z]+']);

        $this->assertTrue($route->matches(CHTTP_Request::create('/user/1/john', 'GET')));
        $this->assertFalse($route->matches(CHTTP_Request::create('/user/1/JOHN', 'GET')));
        $this->assertFalse($route->matches(CHTTP_Request::create('/user/x/john', 'GET')));
    }

    public function testSetWheresAppliesMultipleConstraints() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->setWheres(['id' => '[0-9]+']);

        $this->assertSame(['id' => '[0-9]+'], $route->wheres);
    }

    public function testMatchesReturnsFalseForWrongMethod() {
        $route = $this->makeRoute('POST', 'foo');
        $request = CHTTP_Request::create('/foo', 'GET');

        $this->assertFalse($route->matches($request));
    }

    public function testMatchesIgnoresMethodWhenIncludingMethodIsFalse() {
        $route = $this->makeRoute('POST', 'foo');
        $request = CHTTP_Request::create('/foo', 'GET');

        $this->assertTrue($route->matches($request, false));
    }

    public function testMatchesReturnsFalseForWrongPath() {
        $route = $this->makeRoute('GET', 'foo');
        $request = CHTTP_Request::create('/bar', 'GET');

        $this->assertFalse($route->matches($request));
    }

    // ------------------------------------------------------------------
    // Optional parameters / defaults
    // ------------------------------------------------------------------

    public function testOptionalParameterMatchesWithoutSegment() {
        $route = $this->makeRoute('GET', 'user/{id?}');

        $this->assertTrue($route->matches(CHTTP_Request::create('/user', 'GET')));
        $this->assertTrue($route->matches(CHTTP_Request::create('/user/5', 'GET')));
    }

    public function testOptionalParameterUsesDefaultWhenMissing() {
        $route = $this->makeRoute('GET', 'user/{id?}');
        $route->defaults('id', 'fallback');

        $request = CHTTP_Request::create('/user', 'GET');
        $route->bind($request);

        $this->assertSame('fallback', $route->parameter('id'));
    }

    public function testSetDefaultsReplacesDefaultsArray() {
        $route = $this->makeRoute('GET', 'user/{id?}');
        $route->setDefaults(['id' => 'x']);

        $this->assertSame(['id' => 'x'], $route->defaults);
    }

    public function testGetOptionalParameterNamesFromUri() {
        $route = $this->makeRoute('GET', 'a/{req}/b/{opt?}');
        $symfonyRoute = $route->toSymfonyRoute();

        $this->assertSame(['opt' => null], $symfonyRoute->getDefaults());
    }

    // ------------------------------------------------------------------
    // bind() / parameters()
    // ------------------------------------------------------------------

    public function testBindExtractsParametersFromRequest() {
        $route = $this->makeRoute('GET', 'user/{id}/post/{postId}');
        $request = CHTTP_Request::create('/user/42/post/99', 'GET');

        $route->bind($request);

        $this->assertSame(['id' => '42', 'postId' => '99'], $route->parameters());
    }

    public function testParameterReturnsDefaultWhenMissing() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $this->assertSame('42', $route->parameter('id'));
        $this->assertSame('fallback', $route->parameter('missing', 'fallback'));
        $this->assertNull($route->parameter('missing'));
    }

    public function testHasParameterAndHasParameters() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $this->assertFalse($route->hasParameters());

        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $this->assertTrue($route->hasParameters());
        $this->assertTrue($route->hasParameter('id'));
        $this->assertFalse($route->hasParameter('nope'));
    }

    public function testParametersThrowsLogicExceptionWhenNotBound() {
        $route = $this->makeRoute('GET', 'user/{id}');

        $this->expectException(LogicException::class);
        $route->parameters();
    }

    public function testOriginalParametersThrowsLogicExceptionWhenNotBound() {
        $route = $this->makeRoute('GET', 'user/{id}');

        $this->expectException(LogicException::class);
        $route->originalParameters();
    }

    public function testOriginalParameterReturnsOriginalValueAfterSetParameter() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $route->setParameter('id', '99');

        $this->assertSame('99', $route->parameter('id'));
        $this->assertSame('42', $route->originalParameter('id'));
    }

    public function testSetParameterAddsNewParameter() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $route->setParameter('extra', 'value');

        $this->assertSame('value', $route->parameter('extra'));
    }

    public function testForgetParameterRemovesParameter() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $route->forgetParameter('id');

        $this->assertFalse($route->hasParameter('id'));
    }

    public function testParametersWithoutNullsFiltersNullValues() {
        $route = $this->makeRoute('GET', 'user/{id?}/{name?}');
        $route->bind(CHTTP_Request::create('/user', 'GET'));
        $route->setParameter('id', null);
        $route->setParameter('name', 'john');

        $this->assertSame(['name' => 'john'], $route->parametersWithoutNulls());
    }

    public function testParameterNamesAreExtractedFromUri() {
        $route = $this->makeRoute('GET', 'user/{id}/post/{postId?}');

        $this->assertSame(['id', 'postId'], $route->parameterNames());
    }

    public function testBindIsIdempotentWhenParametersAlreadySet() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $request = CHTTP_Request::create('/user/42', 'GET');

        $route->bind($request);
        $route->setParameter('id', 'changed');
        // Binding again should not overwrite already-set parameters.
        $route->bind($request);

        $this->assertSame('changed', $route->parameter('id'));
    }

    // ------------------------------------------------------------------
    // Domain
    // ------------------------------------------------------------------

    public function testDomainGetterAndSetter() {
        $route = $this->makeRoute('GET', 'foo');
        $this->assertNull($route->domain());

        $route->domain('api.example.com');

        $this->assertSame('api.example.com', $route->domain());
        $this->assertSame('api.example.com', $route->getDomain());
    }

    public function testDomainStripsScheme() {
        $route = $this->makeRoute('GET', 'foo');
        $route->domain('https://api.example.com');

        $this->assertSame('api.example.com', $route->getDomain());
    }

    public function testMatchesRespectsDomain() {
        $route = $this->makeRoute('GET', 'foo');
        $route->domain('api.example.com');

        $matching = CHTTP_Request::create('http://api.example.com/foo', 'GET');
        $nonMatching = CHTTP_Request::create('http://other.example.com/foo', 'GET');

        $this->assertTrue($route->matches($matching));
        $this->assertFalse($route->matches($nonMatching));
    }

    public function testDomainParametersAreExtractedOnBind() {
        $route = $this->makeRoute('GET', 'foo/{id}');
        $route->domain('{account}.example.com');

        $request = CHTTP_Request::create('http://acme.example.com/foo/5', 'GET');
        $route->bind($request);

        $this->assertSame(['account' => 'acme', 'id' => '5'], $route->parameters());
    }

    // ------------------------------------------------------------------
    // Prefix
    // ------------------------------------------------------------------

    public function testPrefixIsPrependedToUri() {
        $route = $this->makeRoute('GET', 'bar');
        $route->prefix('foo');

        $this->assertSame('foo/bar', $route->uri());
        $this->assertSame('foo', $route->getPrefix());
    }

    public function testPrefixHandlesSlashes() {
        $route = $this->makeRoute('GET', '/bar');
        $route->prefix('/foo/');

        $this->assertSame('foo/bar', $route->uri());
    }

    public function testEmptyPrefixLeavesRootUriAsSlash() {
        $route = $this->makeRoute('GET', '/');
        $route->prefix('');

        $this->assertSame('/', $route->uri());
    }

    // ------------------------------------------------------------------
    // http/https only
    // ------------------------------------------------------------------

    public function testSchemeValidationHttpOnly() {
        $route = $this->makeRoute('GET', 'foo', [
            'uses' => function () {
            },
            'http',
        ]);

        $this->assertTrue($route->httpOnly());
        $this->assertTrue($route->matches(CHTTP_Request::create('http://example.com/foo', 'GET')));
        $this->assertFalse($route->matches(CHTTP_Request::create('https://example.com/foo', 'GET')));
    }

    public function testSchemeValidationHttpsOnly() {
        $route = $this->makeRoute('GET', 'foo', [
            'uses' => function () {
            },
            'https',
        ]);

        $this->assertTrue($route->httpsOnly());
        $this->assertTrue($route->secure());
        $this->assertTrue($route->matches(CHTTP_Request::create('https://example.com/foo', 'GET')));
        $this->assertFalse($route->matches(CHTTP_Request::create('http://example.com/foo', 'GET')));
    }

    // ------------------------------------------------------------------
    // Middleware
    // ------------------------------------------------------------------

    public function testMiddlewareGetterDefaultsToEmptyArray() {
        $route = $this->makeRoute('GET', 'foo');

        $this->assertSame([], $route->middleware());
    }

    public function testMiddlewareCanBeSetWithArray() {
        $route = $this->makeRoute('GET', 'foo');
        $route->middleware(['auth', 'throttle']);

        $this->assertSame(['auth', 'throttle'], $route->middleware());
    }

    public function testMiddlewareCanBeSetWithVariadicStrings() {
        $route = $this->makeRoute('GET', 'foo');
        $route->middleware('auth', 'throttle');

        $this->assertSame(['auth', 'throttle'], $route->middleware());
    }

    public function testMiddlewareAccumulatesAcrossCalls() {
        $route = $this->makeRoute('GET', 'foo');
        $route->middleware('auth');
        $route->middleware('throttle');

        $this->assertSame(['auth', 'throttle'], $route->middleware());
    }

    public function testWithoutMiddlewareTracksExcludedMiddleware() {
        $route = $this->makeRoute('GET', 'foo');
        $route->withoutMiddleware('auth');

        $this->assertSame(['auth'], $route->excludedMiddleware());
    }

    public function testWithoutMiddlewareAcceptsArray() {
        $route = $this->makeRoute('GET', 'foo');
        $route->withoutMiddleware(['auth', 'throttle']);

        $this->assertSame(['auth', 'throttle'], $route->excludedMiddleware());
    }

    public function testGatherMiddlewareReturnsRouteMiddlewareForClosureAction() {
        $route = $this->makeRoute('GET', 'foo');
        $route->middleware('auth');

        // Closure-based actions have no controller, so gatherMiddleware()
        // should just return the route's own middleware list.
        $this->assertSame(['auth'], $route->gatherMiddleware());
    }

    public function testGatherMiddlewareIsMemoized() {
        $route = $this->makeRoute('GET', 'foo');
        $route->middleware('auth');

        $first = $route->gatherMiddleware();
        $route->middleware('added-after');
        $second = $route->gatherMiddleware();

        $this->assertSame($first, $second);
    }

    // ------------------------------------------------------------------
    // Fallback
    // ------------------------------------------------------------------

    public function testFallbackFlag() {
        $route = $this->makeRoute('GET', '{fallbackPlaceholder}');
        $this->assertFalse($route->isFallback);

        $route->fallback();
        $this->assertTrue($route->isFallback);

        $route->setFallback(false);
        $this->assertFalse($route->isFallback);
    }

    // ------------------------------------------------------------------
    // block()/withoutBlocking()
    // ------------------------------------------------------------------

    public function testBlockSetsLockAndWaitSeconds() {
        $route = $this->makeRoute('GET', 'foo');
        $route->block(5, 15);

        $this->assertSame(5, $route->locksFor());
        $this->assertSame(15, $route->waitsFor());
    }

    public function testBlockDefaults() {
        $route = $this->makeRoute('GET', 'foo');
        $route->block();

        $this->assertSame(10, $route->locksFor());
        $this->assertSame(10, $route->waitsFor());
    }

    public function testWithoutBlockingClearsLockAndWaitSeconds() {
        $route = $this->makeRoute('GET', 'foo');
        $route->block(5, 15);
        $route->withoutBlocking();

        $this->assertNull($route->locksFor());
        $this->assertNull($route->waitsFor());
    }

    // ------------------------------------------------------------------
    // Action / controller parsing
    // ------------------------------------------------------------------

    public function testActionNameDefaultsToClosure() {
        $route = $this->makeRoute('GET', 'foo');

        $this->assertSame('Closure', $route->getActionName());
    }

    protected function controllerAction($usesString) {
        // Route itself only understands array actions with an already-resolved
        // "uses"/"controller" pair; converting a bare "Controller@method" string
        // into that shape is the router's job (see CRouting_Router::convertToControllerAction),
        // not the Route constructor's.
        return ['uses' => $usesString, 'controller' => $usesString];
    }

    public function testActionNameForControllerString() {
        $route = $this->makeRoute('GET', 'foo', $this->controllerAction('RouteTestDummyController@index'));

        $this->assertSame('RouteTestDummyController@index', $route->getActionName());
        $this->assertSame('index', $route->getActionMethod());
    }

    public function testGetActionReturnsWholeArrayOrKey() {
        $route = $this->makeRoute('GET', 'foo', $this->controllerAction('RouteTestDummyController@index'));

        $this->assertSame('RouteTestDummyController@index', $route->getAction('uses'));
        $this->assertIsArray($route->getAction());
    }

    public function testUsesSetsAction() {
        // uses() delegates namespace-prefix resolution to the router instance
        // via addGroupNamespaceToStringUses(), so a router must be attached first.
        $route = $this->makeRoute('GET', 'foo');
        $route->setRouter(new CRouting_Router());
        $route->uses('RouteTestDummyController@index');

        $this->assertSame('RouteTestDummyController@index', $route->getActionName());
    }

    public function testGetControllerResolvesControllerInstance() {
        $route = $this->makeRoute('GET', 'foo', $this->controllerAction('RouteTestDummyController@index'));

        $controller = $route->getController();

        $this->assertInstanceOf('RouteTestDummyController', $controller);
    }

    public function testRunCallableInvokesClosureWithBoundParameters() {
        $route = $this->makeRoute('GET', 'user/{id}', [
            'uses' => function ($id) {
                return 'user-' . $id;
            },
        ]);
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $this->assertSame('user-42', $route->run());
    }

    public function testMissingActionThrowsLogicExceptionWhenRun() {
        $route = new CRouting_Route(['GET'], 'foo', null);
        $route->bind(CHTTP_Request::create('/foo', 'GET'));

        $this->expectException(LogicException::class);
        $route->run();
    }

    public function testControllerMiddlewareEmptyForClosureAction() {
        $route = $this->makeRoute('GET', 'foo');

        $this->assertSame([], $route->controllerMiddleware());
    }

    // ------------------------------------------------------------------
    // signatureParameters()
    // ------------------------------------------------------------------

    public function testSignatureParametersReturnsClosureParameters() {
        $route = $this->makeRoute('GET', 'user/{id}', [
            'uses' => function (RouteTestDummyDependency $dep, $id) {
            },
        ]);

        $params = $route->signatureParameters();

        $this->assertCount(2, $params);
        $this->assertSame('dep', $params[0]->getName());
        $this->assertSame('id', $params[1]->getName());
    }

    public function testSignatureParametersFilteredBySubClass() {
        // isParameterSubclassOf() checks strict subclass-of, not equality, so the
        // parameter type must extend the filter class rather than match it exactly.
        $route = $this->makeRoute('GET', 'user/{id}', [
            'uses' => function (RouteTestDummyDependency $dep, $id) {
            },
        ]);

        $params = $route->signatureParameters('RouteTestDummyDependencyBase');

        $this->assertCount(1, $params);
        $this->assertSame('dep', reset($params)->getName());
    }

    public function testSignatureParametersForControllerString() {
        $route = $this->makeRoute('GET', 'foo', 'RouteTestDummyController@withArg');

        $params = $route->signatureParameters();

        $this->assertCount(1, $params);
        $this->assertSame('id', $params[0]->getName());
    }

    // ------------------------------------------------------------------
    // RouteUri binding fields ({param:field} syntax)
    // ------------------------------------------------------------------

    public function testRouteUriParseExtractsBindingFields() {
        $parsed = CRouting_RouteUri::parse('user/{user:slug}');

        $this->assertSame('user/{user}', $parsed->uri);
        $this->assertSame(['user' => 'slug'], $parsed->bindingFields);
    }

    public function testRouteUriParseHandlesOptionalBindingField() {
        $parsed = CRouting_RouteUri::parse('user/{user:slug?}');

        $this->assertSame('user/{user?}', $parsed->uri);
        $this->assertSame(['user' => 'slug'], $parsed->bindingFields);
    }

    public function testRouteUriParseLeavesPlainParametersUntouched() {
        $parsed = CRouting_RouteUri::parse('user/{id}');

        $this->assertSame('user/{id}', $parsed->uri);
        $this->assertSame([], $parsed->bindingFields);
    }

    public function testSetUriPopulatesBindingFields() {
        $route = $this->makeRoute('GET', 'foo');
        $route->setUri('user/{user:slug}');

        $this->assertSame('user/{user}', $route->uri());
        $this->assertSame('slug', $route->bindingFieldFor('user'));
    }

    public function testBindingFieldsDefaultsToEmptyArray() {
        $route = $this->makeRoute('GET', 'user/{id}');

        $this->assertSame([], $route->bindingFields());
        $this->assertNull($route->bindingFieldFor('id'));
    }

    // ------------------------------------------------------------------
    // toSymfonyRoute()
    // ------------------------------------------------------------------

    public function testToSymfonyRouteCarriesMethodsAndPath() {
        $route = $this->makeRoute(['GET', 'POST'], 'foo/{bar}');
        $route->where('bar', '[a-z]+');

        $symfonyRoute = $route->toSymfonyRoute();

        $this->assertSame(['GET', 'POST', 'HEAD'], $symfonyRoute->getMethods());
        // Symfony's Route normalizes the path to always start with a leading slash.
        $this->assertSame('/foo/{bar}', $symfonyRoute->getPath());
        $this->assertSame(['bar' => '[a-z]+'], $symfonyRoute->getRequirements());
    }

    // ------------------------------------------------------------------
    // Dynamic parameter access (__get)
    // ------------------------------------------------------------------

    public function testMagicGetReturnsParameterValue() {
        $route = $this->makeRoute('GET', 'user/{id}');
        $route->bind(CHTTP_Request::create('/user/42', 'GET'));

        $this->assertSame('42', $route->id);
    }
}

/**
 * Minimal controller used to exercise controller-string route actions
 * (RouteAction::parse / getController / signatureParameters) without
 * needing a real application controller.
 */
class RouteTestDummyController {
    public function index() {
        return 'index';
    }

    public function withArg($id) {
        return $id;
    }
}

class RouteTestDummyDependencyBase {
}

class RouteTestDummyDependency extends RouteTestDummyDependencyBase {
}

<?php
use PHPUnit\Framework\TestCase;

class ManageEventTraitTest extends TestCase {
    protected function factory() {
        $factory = new CView_Factory();
        $factory->setDispatcher(new CEvent_Dispatcher());

        return $factory;
    }

    protected function view(CView_Factory $factory, $name) {
        return new CView_View(new CView_Engine_PhpEngine(), $name, '/dev/null', []);
    }

    public function testComposerRegistersAClosureAndCallComposerInvokesIt() {
        $factory = $this->factory();
        $called = null;
        $factory->composer('alert', function ($view) use (&$called) {
            $called = $view;
        });

        $view = $this->view($factory, 'alert');
        $factory->callComposer($view);

        $this->assertSame($view, $called);
    }

    public function testComposerReturnsTheRegisteredCallbacksForEachView() {
        $factory = $this->factory();
        $callback = function () {
        };

        $registered = $factory->composer(['alert', 'modal'], $callback);

        $this->assertSame([$callback, $callback], $registered);
    }

    public function testCreatorRegistersAClosureAndCallCreatorInvokesIt() {
        $factory = $this->factory();
        $called = null;
        $factory->creator('alert', function ($view) use (&$called) {
            $called = $view;
        });

        $view = $this->view($factory, 'alert');
        $factory->callCreator($view);

        $this->assertSame($view, $called);
    }

    public function testCallComposerDoesNotInvokeAListenerRegisteredForADifferentView() {
        $factory = $this->factory();
        $called = false;
        $factory->composer('alert', function () use (&$called) {
            $called = true;
        });

        $factory->callComposer($this->view($factory, 'modal'));

        $this->assertFalse($called);
    }

    public function testComposersRegistersMultipleCallbacksKeyedByCallback() {
        // composers()'s array is [callback => views] - since PHP array keys
        // can only be strings/ints, the callback side has to be a class
        // string ("Class@method"), never a Closure object.
        $factory = $this->factory();
        ManageEventTraitTest_FakeComposer::$lastView = null;

        $factory->composers([
            ManageEventTraitTest_FakeComposer::class => ['alert', 'modal'],
        ]);

        $view = $this->view($factory, 'alert');
        $factory->callComposer($view);
        $this->assertSame($view, ManageEventTraitTest_FakeComposer::$lastView);

        $view = $this->view($factory, 'modal');
        $factory->callComposer($view);
        $this->assertSame($view, ManageEventTraitTest_FakeComposer::$lastView);
    }

    public function testComposerAcceptsAWildcardViewName() {
        $factory = $this->factory();
        $called = null;
        $factory->composer('*', function ($view) use (&$called) {
            $called = $view;
        });

        $view = $this->view($factory, 'anything');
        $factory->callComposer($view);

        $this->assertSame($view, $called);
    }

    public function testClassBasedComposerResolvesFromTheContainerAndCallsCompose() {
        // Regression coverage for a real bug: buildClassEventCallback() used
        // to reference an undefined $this->container property (CView_Factory
        // has no such property - only getContainer(), which resolves
        // CContainer::getInstance()), so any string/class-based composer
        // ("Class@method") threw instead of working. Fixed to use
        // $this->getContainer().
        $factory = $this->factory();
        $factory->composer('alert', ManageEventTraitTest_FakeComposer::class);

        $view = $this->view($factory, 'alert');
        $factory->callComposer($view);

        $this->assertSame($view, ManageEventTraitTest_FakeComposer::$lastView);
    }
}

class ManageEventTraitTest_FakeComposer {
    public static $lastView;

    public function compose($view) {
        self::$lastView = $view;
    }
}

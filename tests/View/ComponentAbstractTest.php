<?php
use PHPUnit\Framework\TestCase;

class ComponentAbstractTest extends TestCase {
    protected function tearDown(): void {
        ComponentAbstractTest_Alert::forgetComponentsResolver();
        ComponentAbstractTest_Alert::flushCache();
        CView::finder()->flush();
    }

    public function testResolveConstructsDirectlyWhenAllConstructorParamsAreInData() {
        $component = ComponentAbstractTest_Alert::resolve(['type' => 'error', 'message' => 'Oops']);

        $this->assertInstanceOf(ComponentAbstractTest_Alert::class, $component);
        $this->assertSame('error', $component->type);
        $this->assertSame('Oops', $component->message);
    }

    public function testResolveFallsBackToTheContainerWhenDataIsMissingAConstructorParam() {
        $component = ComponentAbstractTest_Alert::resolve(['type' => 'error']);

        $this->assertInstanceOf(ComponentAbstractTest_Alert::class, $component);
        $this->assertSame('error', $component->type);
        $this->assertNull($component->message);
    }

    public function testResolveUsesTheCustomComponentsResolverWhenSet() {
        $custom = new ComponentAbstractTest_Alert('warning', 'from resolver');
        ComponentAbstractTest_Alert::resolveComponentsUsing(function ($class, $data) use ($custom) {
            return $custom;
        });

        $result = ComponentAbstractTest_Alert::resolve(['type' => 'error', 'message' => 'Oops']);

        $this->assertSame($custom, $result);
    }

    public function testWithNameSetsTheComponentName() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $component->withName('alert');

        $this->assertSame('alert', $component->componentName);
    }

    public function testWithAttributesLazilyCreatesAnAttributeBag() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $component->withAttributes(['class' => 'font-bold']);

        $this->assertInstanceOf(CView_ComponentAttributeBag::class, $component->attributes);
        $this->assertSame('font-bold', $component->attributes->get('class'));
    }

    public function testShouldRenderDefaultsToTrue() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $this->assertTrue($component->shouldRender());
    }

    public function testDataMergesPublicPropertiesAndZeroArgMethodsAsInvokables() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $data = $component->data();

        $this->assertSame('error', $data['type']);
        $this->assertSame('Oops', $data['message']);
        $this->assertInstanceOf(CView_InvokableComponentVariable::class, $data['formattedType']);
        $this->assertSame('ERROR', (string) $data['formattedType']);
    }

    public function testDataExposesMultiArgMethodsAsClosures() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $data = $component->data();

        $this->assertInstanceOf(Closure::class, $data['combine']);
        $this->assertSame('error:Oops', $data['combine']('error', 'Oops'));
    }

    public function testDataOmitsDunderPrefixedAndFrameworkMethods() {
        $component = new ComponentAbstractTest_Alert('error', 'Oops');

        $data = $component->data();

        $this->assertArrayNotHasKey('render', $data);
        $this->assertArrayNotHasKey('resolveView', $data);
        $this->assertArrayNotHasKey('shouldRender', $data);
        $this->assertArrayNotHasKey('withName', $data);
        $this->assertArrayNotHasKey('withAttributes', $data);
    }

    public function testResolveViewReturnsAnExistingViewNameUnchanged() {
        $dir = sys_get_temp_dir() . '/cf_component_abstract_test_' . uniqid();
        mkdir($dir, 0777, true);
        $viewName = 'existing_' . uniqid();
        file_put_contents($dir . "/{$viewName}.php", 'x');
        CView::finder()->addLocation($dir);

        try {
            $component = new ComponentAbstractTest_ViewComponent($viewName);

            $this->assertSame($viewName, $component->resolveView());
        } finally {
            unlink($dir . "/{$viewName}.php");
            rmdir($dir);
        }
    }

    public function testResolveViewCreatesABladeViewFromRawStringContent() {
        $component = new ComponentAbstractTest_ViewComponent('<div>{{ $slot }}</div>');

        $resolved = $component->resolveView();

        $this->assertStringStartsWith('__components::', $resolved);
        $this->assertTrue(CView::factory()->exists($resolved));
    }

    public function testResolveViewCachesTheGeneratedViewNameForIdenticalContent() {
        $component1 = new ComponentAbstractTest_ViewComponent('<div>same content</div>');
        $component2 = new ComponentAbstractTest_ViewComponent('<div>same content</div>');

        $this->assertSame($component1->resolveView(), $component2->resolveView());
    }

    public function testResolveViewPassesThroughAViewInstanceUnchanged() {
        $view = CView::factory()->file(__FILE__);
        $component = new ComponentAbstractTest_ViewComponent($view);

        $this->assertSame($view, $component->resolveView());
    }

    public function testResolveViewWrapsAClosureAndResolvesItsReturnValue() {
        $component = new ComponentAbstractTest_ViewComponent(function ($data) {
            return '<span>closure component</span>';
        });

        $resolvedClosure = $component->resolveView();
        $this->assertInstanceOf(Closure::class, $resolvedClosure);

        $result = $resolvedClosure([]);
        $this->assertStringStartsWith('__components::', $result);
    }

    public function testViewHelperDelegatesToTheGlobalFactory() {
        $dir = sys_get_temp_dir() . '/cf_component_abstract_test_' . uniqid();
        mkdir($dir, 0777, true);
        $viewName = 'helper_' . uniqid();
        file_put_contents($dir . "/{$viewName}.php", '<?php echo "via view helper"; ?>');
        CView::finder()->addLocation($dir);

        try {
            $component = new ComponentAbstractTest_Alert('error', 'Oops');

            $this->assertSame('via view helper', $component->view($viewName)->render());
        } finally {
            unlink($dir . "/{$viewName}.php");
            rmdir($dir);
        }
    }
}

class ComponentAbstractTest_Alert extends CView_ComponentAbstract {
    public $type;

    public $message;

    public function __construct($type, $message = null) {
        $this->type = $type;
        $this->message = $message;
    }

    public function render() {
        return 'alert';
    }

    public function formattedType() {
        return strtoupper($this->type);
    }

    public function combine($a, $b) {
        return $a . ':' . $b;
    }
}

class ComponentAbstractTest_ViewComponent extends CView_ComponentAbstract {
    protected $view;

    public function __construct($view) {
        $this->view = $view;
    }

    public function render() {
        return $this->view;
    }
}

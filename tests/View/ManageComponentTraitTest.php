<?php
use PHPUnit\Framework\TestCase;

class ManageComponentTraitTest extends TestCase {
    /** @var string */
    protected $dir;

    protected function setUp(): void {
        $this->dir = sys_get_temp_dir() . '/cf_component_test_' . uniqid();
        mkdir($this->dir, 0777, true);
        // Registered on the real global finder - CView_Factory's
        // renderComponent() always resolves views through CView::finder(),
        // regardless of which Factory instance started the component.
        CView::finder()->addLocation($this->dir);
    }

    protected function tearDown(): void {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->dir);
        // CView::finder() is a global singleton that caches resolved paths
        // by view name (Finder::$views) - without flushing, a later test
        // reusing the same view name (e.g. "alert") would be served the
        // now-deleted path from this test's temp directory.
        CView::finder()->flush();
    }

    protected function factory() {
        return new CView_Factory();
    }

    protected function makeView($name, $php) {
        file_put_contents($this->dir . "/{$name}.php", $php);
    }

    public function testStartComponentAndRenderComponentRendersAViewByName() {
        $this->makeView('alert', '<?php echo "type=" . $type; ?>');
        $factory = $this->factory();

        $factory->startComponent('alert', ['type' => 'error']);
        $result = $factory->renderComponent();

        $this->assertSame('type=error', $result);
    }

    public function testDefaultSlotContentIsPassedAsTheSlotVariable() {
        $this->makeView('alert', '<?php echo $slot; ?>');
        $factory = $this->factory();

        $factory->startComponent('alert');
        echo 'default body content';
        $result = $factory->renderComponent();

        $this->assertSame('default body content', $result);
    }

    public function testNamedSlotIsAvailableAsAViewVariable() {
        $this->makeView('alert', '<?php echo $title; ?> / <?php echo $slot; ?>');
        $factory = $this->factory();

        $factory->startComponent('alert');
        $factory->slot('title');
        echo 'Warning';
        $factory->endSlot();
        echo 'Body';
        $result = $factory->renderComponent();

        $this->assertSame('Warning / Body', $result);
    }

    public function testInlineSlotContentIsUsedDirectlyWithoutOutputBuffering() {
        $factory = $this->factory();
        $this->makeView('alert', '<?php echo $title; ?>');

        $factory->startComponent('alert');
        $factory->slot('title', 'Inline Title');
        $result = $factory->renderComponent();

        $this->assertSame('Inline Title', $result);
    }

    public function testStartComponentAcceptsAClosureThatReturnsAViewName() {
        $this->makeView('alert', '<?php echo "closure-rendered"; ?>');
        $factory = $this->factory();

        $factory->startComponent(function ($data) {
            return 'alert';
        });
        $result = $factory->renderComponent();

        $this->assertSame('closure-rendered', $result);
    }

    public function testStartComponentAcceptsAnHtmlableInstance() {
        // Note: renderComponent() checks "instanceof CInterface_Htmlable"
        // (a CF-specific interface that extends the plain Illuminate
        // Htmlable), but CBase_HtmlString itself only implements the plain
        // Illuminate\Contracts\Support\Htmlable - so a CBase_HtmlString
        // passed to startComponent() does NOT satisfy this check and falls
        // through to being treated as a view name instead. Using a double
        // that implements the narrower interface directly, matching what
        // renderComponent() actually checks for.
        $factory = $this->factory();
        $htmlable = new ManageComponentTraitTest_Htmlable('<b>raw html</b>');

        $factory->startComponent($htmlable);
        $result = $factory->renderComponent();

        $this->assertSame('<b>raw html</b>', $result);
    }

    public function testStartComponentFirstUsesTheFirstExistingView() {
        $this->makeView('real-alert', '<?php echo "found"; ?>');
        $factory = $this->factory();

        $factory->startComponentFirst(['missing-alert', 'real-alert']);
        $result = $factory->renderComponent();

        $this->assertSame('found', $result);
    }

    public function testGetConsumableComponentDataFindsDataFromAnAncestorComponent() {
        $this->makeView('outer', '<?php echo "x"; ?>');
        $factory = $this->factory();

        $factory->startComponent('outer', ['color' => 'blue']);
        // While "outer" is still on the component stack (mid-render), an
        // inner @aware lookup should be able to see its data.
        $this->assertSame('blue', $factory->getConsumableComponentData('color'));
        $this->assertSame('fallback', $factory->getConsumableComponentData('missing', 'fallback'));
        $factory->renderComponent();
    }

    public function testGetConsumableComponentDataReturnsDefaultWhenStackIsEmpty() {
        $factory = $this->factory();

        $this->assertNull($factory->getConsumableComponentData('color'));
        $this->assertSame('fallback', $factory->getConsumableComponentData('color', 'fallback'));
    }

    public function testNestedComponentsRenderIndependently() {
        $this->makeView('outer', '<?php echo "outer(" . $slot . ")"; ?>');
        $this->makeView('inner', '<?php echo "inner"; ?>');
        $factory = $this->factory();

        $factory->startComponent('outer');
        $factory->startComponent('inner');
        $innerResult = $factory->renderComponent();
        echo $innerResult;
        $outerResult = $factory->renderComponent();

        $this->assertSame('inner', $innerResult);
        $this->assertSame('outer(inner)', $outerResult);
    }
}

class ManageComponentTraitTest_Htmlable implements CInterface_Htmlable {
    protected $html;

    public function __construct($html) {
        $this->html = $html;
    }

    public function toHtml() {
        return $this->html;
    }
}

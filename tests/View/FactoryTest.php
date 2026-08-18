<?php
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase {
    /** @var string */
    protected $dir;

    protected function setUp(): void {
        $this->dir = sys_get_temp_dir() . '/cf_factory_test_' . uniqid();
        mkdir($this->dir, 0777, true);
        CView::finder()->addLocation($this->dir);
    }

    protected function tearDown(): void {
        foreach (glob($this->dir . '/*') ?: [] as $file) {
            unlink($file);
        }
        rmdir($this->dir);
        CView::finder()->flush();
    }

    protected function factory() {
        return new CView_Factory();
    }

    protected function makeView($name, $php) {
        file_put_contents($this->dir . "/{$name}.php", $php);
    }

    public function testMakeReturnsAViewInstanceForARegisteredView() {
        $this->makeView('greeting', '<?php echo "hi " . $name; ?>');
        $factory = $this->factory();

        $view = $factory->make('greeting', ['name' => 'Hery']);

        $this->assertInstanceOf(CView_View::class, $view);
        $this->assertSame('greeting', $view->getName());
        $this->assertSame('hi Hery', $view->render());
    }

    public function testMakeThrowsForAMissingView() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);

        $factory->make('totally-missing-view');
    }

    public function testFileRendersAViewDirectlyByPath() {
        $this->makeView('raw', '<?php echo "raw " . $x; ?>');
        $factory = $this->factory();

        $view = $factory->file($this->dir . '/raw.php', ['x' => 1]);

        $this->assertSame('raw 1', $view->render());
    }

    public function testExistsReturnsTrueForARegisteredViewAndFalseOtherwise() {
        $this->makeView('greeting', 'hi');
        $factory = $this->factory();

        $this->assertTrue($factory->exists('greeting'));
        $this->assertFalse($factory->exists('totally-missing-view'));
    }

    public function testFirstReturnsTheFirstExistingViewFromTheList() {
        $this->makeView('second', '<?php echo "second"; ?>');
        $factory = $this->factory();

        $view = $factory->first(['first-missing', 'second']);

        $this->assertSame('second', $view->render());
    }

    public function testFirstThrowsWhenNoneOfTheViewsExist() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('None of the views in the given array exist.');

        $factory->first(['missing-one', 'missing-two']);
    }

    public function testRenderWhenRendersOnlyWhenConditionIsTrue() {
        $this->makeView('greeting', '<?php echo "hi"; ?>');
        $factory = $this->factory();

        $this->assertSame('hi', $factory->renderWhen(true, 'greeting'));
        $this->assertSame('', $factory->renderWhen(false, 'greeting'));
    }

    public function testRenderUnlessRendersOnlyWhenConditionIsFalse() {
        $this->makeView('greeting', '<?php echo "hi"; ?>');
        $factory = $this->factory();

        $this->assertSame('hi', $factory->renderUnless(false, 'greeting'));
        $this->assertSame('', $factory->renderUnless(true, 'greeting'));
    }

    public function testRenderEachRendersAPartialForEveryDataItem() {
        $this->makeView('item', '<?php echo $item . "-" . $key; ?>');
        $factory = $this->factory();

        $result = $factory->renderEach('item', ['a', 'b'], 'item');

        $this->assertSame('a-0b-1', $result);
    }

    public function testRenderEachRendersTheEmptyViewWhenDataIsEmpty() {
        $this->makeView('empty-state', '<?php echo "nothing here"; ?>');
        $factory = $this->factory();

        $result = $factory->renderEach('item', [], 'item', 'empty-state');

        $this->assertSame('nothing here', $result);
    }

    public function testRenderEachUsesARawStringForTheEmptyCaseWhenPrefixed() {
        $factory = $this->factory();

        $result = $factory->renderEach('item', [], 'item', 'raw|literally nothing');

        $this->assertSame('literally nothing', $result);
    }

    public function testShareOnAFreshFactoryInstanceIsInvisibleToRenderedViews() {
        // CView_View::gatherData() always reads CView::factory()->getShared()
        // - the GLOBAL singleton - never the Factory instance that actually
        // created the view via make()/viewInstance(). So share() on a fresh
        // `new CView_Factory()` (as used throughout this file for state
        // isolation) has no effect on what a view sees when rendered; only
        // sharing on the real singleton does.
        $key = 'appName_' . uniqid();
        $this->makeView('greeting', "<?php echo \${$key} ?? 'MISSING'; ?>");
        $factory = $this->factory();
        $factory->share($key, 'CF');

        $this->assertSame('CF', $factory->shared($key));
        $this->assertSame('MISSING', $factory->make('greeting')->render());
    }

    public function testShareOnTheGlobalSingletonIsVisibleToRenderedViews() {
        $key = 'appName_' . uniqid();
        $this->makeView('greeting', "<?php echo \${$key}; ?>");
        CView::factory()->share($key, 'CF');

        $this->assertSame('CF', CView::factory()->make('greeting')->render());
    }

    public function testShareAcceptsAnArrayOfKeyValuePairs() {
        $factory = $this->factory();
        $factory->share(['a' => 1, 'b' => 2]);

        $this->assertSame(['a' => 1, 'b' => 2], array_intersect_key($factory->getShared(), ['a' => 1, 'b' => 1]));
    }

    public function testSharedReturnsDefaultWhenKeyIsMissing() {
        $factory = $this->factory();

        $this->assertSame('fallback', $factory->shared('missing', 'fallback'));
    }

    public function testGetEngineFromPathResolvesTheCorrectEngine() {
        $factory = $this->factory();

        $this->assertInstanceOf(CView_Engine_CompilerEngine::class, $factory->getEngineFromPath('view.blade.php'));
        $this->assertInstanceOf(CView_Engine_PhpEngine::class, $factory->getEngineFromPath('view.php'));
        $this->assertInstanceOf(CView_Engine_FileEngine::class, $factory->getEngineFromPath('view.css'));
        $this->assertInstanceOf(CView_Engine_FileEngine::class, $factory->getEngineFromPath('view.html'));
    }

    public function testGetEngineFromPathThrowsForAnUnrecognizedExtension() {
        $factory = $this->factory();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unrecognized extension in file: view.xyz.');

        $factory->getEngineFromPath('view.xyz');
    }

    public function testAddExtensionRegistersANewEngineBinding() {
        $factory = $this->factory();
        $factory->addExtension('twig', 'twig-engine', function () {
            return new stdClass();
        });

        $this->assertSame('twig-engine', $factory->getExtensions()['twig']);
        $this->assertInstanceOf(stdClass::class, CView::engineResolver()->resolve('twig-engine'));
    }

    public function testHasRenderedOnceAndMarkAsRenderedOnce() {
        $factory = $this->factory();

        $this->assertFalse($factory->hasRenderedOnce('token-1'));

        $factory->markAsRenderedOnce('token-1');

        $this->assertTrue($factory->hasRenderedOnce('token-1'));
    }

    public function testRenderCountingAndDoneRendering() {
        $factory = $this->factory();

        $this->assertTrue($factory->doneRendering());

        $factory->incrementRender();
        $this->assertFalse($factory->doneRendering());

        $factory->decrementRender();
        $this->assertTrue($factory->doneRendering());
    }

    public function testFlushStateResetsRenderCountRenderedOnceAndSections() {
        $factory = $this->factory();
        $factory->incrementRender();
        $factory->markAsRenderedOnce('token-1');
        $factory->startSection('content', 'body');

        $factory->flushState();

        $this->assertTrue($factory->doneRendering());
        $this->assertFalse($factory->hasRenderedOnce('token-1'));
        $this->assertSame([], $factory->getSections());
    }

    public function testSetDispatcherAndGetDispatcher() {
        $factory = $this->factory();
        $dispatcher = new CEvent_Dispatcher();

        $factory->setDispatcher($dispatcher);

        $this->assertSame($dispatcher, $factory->getDispatcher());
    }

    public function testGetContainerReturnsTheGlobalContainerInstance() {
        $factory = $this->factory();

        $this->assertSame(CContainer::getInstance(), $factory->getContainer());
    }

    public function testGetFinderAlwaysReturnsTheGlobalFinderRegardlessOfSetFinder() {
        // Documents a real quirk: setFinder() assigns CView_Factory's own
        // $finder property, but getFinder() (and every other method that
        // resolves views: make(), exists(), addLocation(), etc.) always
        // calls CView::finder() directly instead - so setFinder() has no
        // observable effect anywhere in this class.
        $factory = $this->factory();
        $customFinder = new CView_Finder();

        $factory->setFinder($customFinder);

        $this->assertNotSame($customFinder, $factory->getFinder());
        $this->assertSame(CView::finder(), $factory->getFinder());
    }

    public function testAddLocationDelegatesToTheGlobalFinder() {
        $factory = $this->factory();
        $newDir = sys_get_temp_dir() . '/cf_factory_test_extra_' . uniqid();
        mkdir($newDir);

        try {
            $factory->addLocation($newDir);

            $this->assertContains(realpath($newDir), CView::finder()->getPaths());
        } finally {
            rmdir($newDir);
        }
    }

    public function testAddNamespaceDelegatesToTheGlobalFinder() {
        $factory = $this->factory();

        $factory->addNamespace('test-ns', $this->dir);

        $this->assertArrayHasKey('test-ns', CView::finder()->getHints());
    }
}

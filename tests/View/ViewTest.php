<?php
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase {
    /** @var string */
    protected $dir;

    protected function setUp(): void {
        $this->dir = sys_get_temp_dir() . '/cf_view_test_' . uniqid();
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

    protected function makeView($name, $php) {
        file_put_contents($this->dir . "/{$name}.php", $php);
    }

    protected function view($name, $data = []) {
        // render()/gatherData() always consult the global CView::factory()
        // singleton (see FactoryTest::testShareOnAFreshFactoryInstanceIsInvisibleToRenderedViews),
        // so views under test are made through it too.
        return CView::factory()->make($name, $data);
    }

    public function testConstructorStoresEngineNamePathAndData() {
        $engine = new CView_Engine_PhpEngine();
        $view = new CView_View($engine, 'greeting', '/some/path.php', ['name' => 'Hery']);

        $this->assertSame($engine, $view->getEngine());
        $this->assertSame('greeting', $view->getName());
        $this->assertSame('greeting', $view->name());
        $this->assertSame('/some/path.php', $view->getPath());
        $this->assertSame(['name' => 'Hery'], $view->getData());
    }

    public function testConstructorAcceptsAnArrayableAsData() {
        $arrayable = new class implements Illuminate\Contracts\Support\Arrayable {
            public function toArray() {
                return ['from' => 'arrayable'];
            }
        };
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php', $arrayable);

        $this->assertSame(['from' => 'arrayable'], $view->getData());
    }

    public function testWithMergesAnArrayOfData() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php', ['a' => 1]);

        $view->with(['b' => 2]);

        $this->assertSame(['a' => 1, 'b' => 2], $view->getData());
    }

    public function testWithSetsASingleKey() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php');

        $view->with('name', 'Hery');

        $this->assertSame(['name' => 'Hery'], $view->getData());
    }

    public function testSetAndSetDataAreAliasesForWith() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php');

        $view->set(['a' => 1]);
        $view->setData(['b' => 2]);

        $this->assertSame(['a' => 1, 'b' => 2], $view->getData());
    }

    public function testWithErrorsPutsAMessageBagUnderTheGivenBagName() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php');

        $view->withErrors(['email' => 'The email field is required.']);

        $errors = $view->getData()['errors'];
        $this->assertInstanceOf(CBase_ViewErrorBag::class, $errors);
        $this->assertTrue($errors->hasBag('default'));
        $this->assertSame('The email field is required.', $errors->first('email'));
    }

    public function testWithErrorsSupportsACustomBagName() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php');

        $view->withErrors(['name' => 'Required.'], 'registration');

        $errors = $view->getData()['errors'];
        $this->assertTrue($errors->hasBag('registration'));
        $this->assertFalse($errors->hasBag('default'));
    }

    public function testArrayAccessGetSetExistsUnset() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php', ['a' => 1]);

        $this->assertTrue(isset($view['a']));
        $this->assertSame(1, $view['a']);

        $view['b'] = 2;
        $this->assertSame(2, $view['b']);

        unset($view['a']);
        $this->assertFalse(isset($view['a']));
    }

    public function testMagicGetSetIssetUnset() {
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php', ['a' => 1]);

        $this->assertSame(1, $view->a);
        $this->assertTrue(isset($view->a));

        $view->b = 2;
        $this->assertSame(2, $view->b);

        unset($view->a);
        $this->assertFalse(isset($view->a));
    }

    public function testRenderEvaluatesTheViewThroughItsEngine() {
        $this->makeView('greeting', '<?php echo "hi " . $name; ?>');

        $result = $this->view('greeting', ['name' => 'Hery'])->render();

        $this->assertSame('hi Hery', $result);
    }

    public function testToHtmlAndToStringAreAliasesForRender() {
        $this->makeView('greeting', '<?php echo "hi"; ?>');
        $view = $this->view('greeting');

        $this->assertSame('hi', $view->toHtml());
        $this->assertSame('hi', (string) $view);
    }

    public function testRenderPassesAResponseThroughTheGivenCallback() {
        $this->makeView('greeting', '<?php echo "hi"; ?>');
        $view = $this->view('greeting');

        $result = $view->render(function ($view, $contents) {
            return strtoupper($contents);
        });

        $this->assertSame('HI', $result);
    }

    public function testRenderFlushesStateAndRethrowsOnException() {
        $this->makeView('broken', '<?php throw new RuntimeException("boom"); ?>');
        $view = $this->view('broken');

        CView::factory()->startSection('leftover', 'should not survive');

        try {
            $view->render();
            $this->fail('Expected an exception to be thrown.');
        } catch (Throwable $e) {
            // The exception is wrapped by CView_Engine_PhpEngine's exception
            // handling, but the important part is that flushState() ran.
            $this->assertFalse(CView::factory()->hasSection('leftover'));
        }
    }

    public function testGatherDataMergesGlobalSharedDataWithTheViewsOwnData() {
        $key = 'shared_' . uniqid();
        CView::factory()->share($key, 'shared-value');
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php', ['own' => 'own-value']);

        $data = $view->gatherData();

        $this->assertSame('shared-value', $data[$key]);
        $this->assertSame('own-value', $data['own']);
    }

    public function testNestAddsAMakeableSubView() {
        // A plain "child" name collides with the real
        // application/cresenity/default/views/child.blade.php - findInPaths()
        // searches the framework's real view directories first, so a
        // sufficiently unique name is needed to guarantee hitting this
        // test's own temp view instead.
        $name = 'child_' . uniqid();
        $this->makeView($name, '<?php echo "child content"; ?>');
        $view = new CView_View(new CView_Engine_PhpEngine(), 'v', '/p.php');

        $view->nest('section', $name);

        $this->assertInstanceOf(CView_View::class, $view->getData()['section']);
        $this->assertSame('child content', $view->getData()['section']->render());
    }
}

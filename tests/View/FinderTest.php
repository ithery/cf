<?php
use PHPUnit\Framework\TestCase;

class FinderTest extends TestCase {
    /** @var string */
    protected $dir;

    /** @var string */
    protected $hintDir;

    protected function setUp(): void {
        $this->dir = sys_get_temp_dir() . '/cf_finder_test_' . uniqid();
        $this->hintDir = sys_get_temp_dir() . '/cf_finder_test_hint_' . uniqid();
        mkdir($this->dir, 0777, true);
        mkdir($this->hintDir, 0777, true);
    }

    protected function tearDown(): void {
        $this->removeDirectory($this->dir);
        $this->removeDirectory($this->hintDir);
    }

    protected function removeDirectory($dir) {
        foreach (glob($dir . '/*') ?: [] as $path) {
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    protected function finder() {
        return new CView_Finder();
    }

    public function testFindLocatesABladePhpFileByDefault() {
        file_put_contents($this->dir . '/alert.blade.php', 'blade');

        $finder = $this->finder();
        $finder->addLocation($this->dir);

        $this->assertSame($this->dir . '/alert.blade.php', $finder->find('alert'));
    }

    public function testFindPrefersBladePhpOverPlainPhpOverCssOverHtml() {
        file_put_contents($this->dir . '/alert.php', 'php');
        file_put_contents($this->dir . '/alert.blade.php', 'blade');
        file_put_contents($this->dir . '/alert.html', 'html');

        $finder = $this->finder();
        $finder->addLocation($this->dir);

        $this->assertSame($this->dir . '/alert.blade.php', $finder->find('alert'));
    }

    public function testFindSupportsDotNotationForSubdirectories() {
        mkdir($this->dir . '/partials');
        file_put_contents($this->dir . '/partials/header.blade.php', 'blade');

        $finder = $this->finder();
        $finder->addLocation($this->dir);

        $this->assertSame($this->dir . '/partials/header.blade.php', $finder->find('partials.header'));
    }

    public function testFindThrowsForAMissingView() {
        $finder = $this->finder();
        $finder->addLocation($this->dir);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('View [totally.missing] not found.');

        $finder->find('totally.missing');
    }

    public function testFindCachesTheResolvedPathAcrossDeletion() {
        file_put_contents($this->dir . '/alert.blade.php', 'blade');

        $finder = $this->finder();
        $finder->addLocation($this->dir);

        $first = $finder->find('alert');
        unlink($this->dir . '/alert.blade.php');

        // find() serves the second call from its internal $views cache
        // without touching the filesystem again.
        $this->assertSame($first, $finder->find('alert'));
    }

    public function testFlushClearsTheResolvedViewCache() {
        file_put_contents($this->dir . '/alert.blade.php', 'blade');

        $finder = $this->finder();
        $finder->addLocation($this->dir);
        $finder->find('alert');
        unlink($this->dir . '/alert.blade.php');

        $finder->flush();

        $this->expectException(InvalidArgumentException::class);
        $finder->find('alert');
    }

    public function testAddNamespaceAndFindANamespacedView() {
        file_put_contents($this->hintDir . '/alert.blade.php', 'blade');

        $finder = $this->finder();
        $finder->addNamespace('vendor', $this->hintDir);

        $this->assertSame($this->hintDir . '/alert.blade.php', $finder->find('vendor::alert'));
    }

    public function testHasHintInformationDetectsTheDoubleColonDelimiter() {
        $finder = $this->finder();

        $this->assertTrue($finder->hasHintInformation('vendor::alert'));
        $this->assertFalse($finder->hasHintInformation('alert'));
    }

    public function testFindNamespacedViewThrowsWhenNamespaceIsUnregistered() {
        $finder = $this->finder();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No hint path defined for [missing].');

        $finder->find('missing::alert');
    }

    public function testAddNamespaceAppendsToAnExistingNamespacesHints() {
        $finder = $this->finder();
        $finder->addNamespace('vendor', '/first');
        $finder->addNamespace('vendor', '/second');

        $this->assertSame(['/first', '/second'], $finder->getHints()['vendor']);
    }

    public function testPrependNamespacePrependsToAnExistingNamespacesHints() {
        $finder = $this->finder();
        $finder->addNamespace('vendor', '/first');
        $finder->prependNamespace('vendor', '/second');

        $this->assertSame(['/second', '/first'], $finder->getHints()['vendor']);
    }

    public function testReplaceNamespaceOverwritesExistingHints() {
        $finder = $this->finder();
        $finder->addNamespace('vendor', '/first');
        $finder->replaceNamespace('vendor', '/only');

        $this->assertSame(['/only'], $finder->getHints()['vendor']);
    }

    public function testPrependLocationAddsBeforeExistingLocations() {
        $finder = $this->finder();
        $finder->addLocation('/a');
        $finder->prependLocation('/b');

        $this->assertSame(['/b', '/a'], $finder->getPaths());
    }

    public function testAddExtensionPrependsAndDeduplicatesAnExistingExtension() {
        $finder = $this->finder();
        $originalCount = count($finder->getExtensions());

        $finder->addExtension('blade.php');

        $extensions = $finder->getExtensions();
        $this->assertSame('blade.php', $extensions[0]);
        $this->assertCount($originalCount, $extensions);
    }

    public function testAddExtensionPrependsANewExtension() {
        $finder = $this->finder();

        $finder->addExtension('twig');

        $this->assertSame('twig', $finder->getExtensions()[0]);
    }

    public function testSetPathsReplacesTheActivePaths() {
        $finder = $this->finder();
        $finder->addLocation('/a');
        $finder->setPaths(['/b', '/c']);

        $this->assertSame(['/b', '/c'], $finder->getPaths());
    }
}

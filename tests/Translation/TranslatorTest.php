<?php
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase {
    /**
     * @var CTranslation_Loader_ArrayLoader
     */
    protected $loader;

    protected function setUp(): void {
        $this->loader = new CTranslation_Loader_ArrayLoader();
    }

    /**
     * @param string $locale
     *
     * @return CTranslation_Translator
     */
    protected function makeTranslator($locale = 'en_US') {
        return new CTranslation_Translator($this->loader, $locale);
    }

    public function testReturnsExactTranslationForCurrentLocale() {
        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $translator = $this->makeTranslator('en_US');

        $this->assertSame('Hello', $translator->get('greeting.hello'));
    }

    public function testReplacesPlaceholdersWhenProvided() {
        $this->loader->addMessages('en_US', 'upload', [
            'tooLarge' => 'File Size is more than :sizeMB MB',
        ]);
        $translator = $this->makeTranslator('en_US');

        $this->assertSame(
            'File Size is more than 5 MB',
            $translator->get('upload.tooLarge', ['sizeMB' => 5])
        );
    }

    public function testLeavesUnknownPlaceholdersIntactWhenNoReplacementGiven() {
        // This is the exact behavior CElement_FormInput_File/FileAjax rely on:
        // fetch a template with its :placeholder tokens still literal, embed it
        // in cres-config, and let the client substitute values it only knows at
        // runtime (the actual selected file name, etc).
        $this->loader->addMessages('en_US', 'upload', [
            'tooLarge' => 'File Size is more than :sizeMB MB',
        ]);
        $translator = $this->makeTranslator('en_US');

        $this->assertSame(
            'File Size is more than :sizeMB MB',
            $translator->get('upload.tooLarge')
        );
    }

    public function testFallsBackToFallbackLocaleWhenKeyMissingInCurrentLocale() {
        // id_ID intentionally has no 'greeting' group at all.
        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $translator = $this->makeTranslator('id_ID');
        $translator->setFallback('en_US');

        $this->assertSame('Hello', $translator->get('greeting.hello'));
    }

    public function testCurrentLocaleWinsOverFallbackWhenBothHaveTheKey() {
        $this->loader->addMessages('id_ID', 'greeting', ['hello' => 'Halo']);
        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $translator = $this->makeTranslator('id_ID');
        $translator->setFallback('en_US');

        $this->assertSame('Halo', $translator->get('greeting.hello'));
    }

    public function testReturnsRawKeyWhenMissingFromEveryLocale() {
        $translator = $this->makeTranslator('id_ID');
        $translator->setFallback('en_US');

        $this->assertSame('greeting.hello', $translator->get('greeting.hello'));
    }

    public function testHasReturnsFalseForAMissingKey() {
        $translator = $this->makeTranslator('en_US');

        $this->assertFalse($translator->has('greeting.hello'));
    }

    public function testHasReturnsTrueWhenTheGroupIsSeededBeforeFirstAccess() {
        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $translator = $this->makeTranslator('en_US');

        $this->assertTrue($translator->has('greeting.hello'));
    }

    public function testLoadedGroupsAreMemoizedPerTranslatorInstance() {
        // Translator::load() caches a (namespace, group, locale) lookup the first
        // time it's touched -- even a miss -- and never re-queries the loader
        // for that combination again on the same instance. Real request
        // lifecycles only ever load a group once, so this isn't a bug, but it
        // does mean addMessages() after a group has already been queried has
        // no effect on that translator instance.
        $translator = $this->makeTranslator('en_US');
        $this->assertFalse($translator->has('greeting.hello'));

        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $this->assertFalse($translator->has('greeting.hello'), 'still false: the miss for this group was already cached');
    }

    public function testSupportsSlashSeparatedGroupNamesLikeElementFile() {
        // Real i18n files live at system/i18n/{locale}/element/file.php, i.e. the
        // "group" portion of the key is "element/file", not just "file".
        $this->loader->addMessages('en_US', 'element/file', [
            'selectFile' => 'Select File',
        ]);
        $translator = $this->makeTranslator('en_US');

        $this->assertSame('Select File', $translator->get('element/file.selectFile'));
    }

    public function testNoFallbackAttemptWhenFallbackDisabled() {
        $this->loader->addMessages('en_US', 'greeting', ['hello' => 'Hello']);
        $translator = $this->makeTranslator('id_ID');
        $translator->setFallback('en_US');

        $this->assertSame(
            'greeting.hello',
            $translator->get('greeting.hello', [], null, false)
        );
    }
}

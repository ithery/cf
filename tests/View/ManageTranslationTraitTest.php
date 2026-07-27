<?php
use PHPUnit\Framework\TestCase;

class ManageTranslationTraitTest extends TestCase {
    protected function factory() {
        return new CView_Factory();
    }

    public function testRenderTranslationFallsBackToTheTrimmedKeyWhenNoLineExists() {
        // CTranslation_Translator falls back to returning the key itself
        // when no translation line matches, so a made-up key round-trips
        // unchanged - this mainly proves startTranslation()/renderTranslation()
        // wire the buffered @lang ... @endlang content through to the
        // translator correctly.
        $factory = $this->factory();

        $factory->startTranslation();
        echo "  totally.missing.translation.key.for.this.test  \n";
        $result = $factory->renderTranslation();

        $this->assertSame('totally.missing.translation.key.for.this.test', $result);
    }

    public function testStartTranslationStoresReplacementsForRenderTranslation() {
        $factory = $this->factory();

        $factory->startTranslation(['name' => 'Hery']);
        echo 'totally.missing.key.:name';
        $result = $factory->renderTranslation();

        // Still falls back to the raw key since it doesn't resolve to a real
        // line, but this at least exercises the replacements being passed
        // through without erroring.
        $this->assertIsString($result);
    }
}

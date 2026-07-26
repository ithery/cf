<?php
use PHPUnit\Framework\TestCase;

/**
 * Scans the real translation data under system/i18n/ (not fixtures) --
 * this is a data-integrity check, not a CTranslation_Translator unit test
 * (see TranslatorTest for that).
 */
class I18nDataIntegrityTest extends TestCase {
    /**
     * @var string
     */
    protected $i18nPath;

    protected function setUp(): void {
        $this->i18nPath = rtrim(SYSPATH, '/') . '/i18n';
    }

    /**
     * @return string[] locale directory names directly under system/i18n/
     */
    protected function locales() {
        return self::localesStatic();
    }

    /**
     * @param string $locale
     *
     * @return string[] group names (eg. "element/file") found under a locale, relative path without .php
     */
    protected function groupsForLocale($locale) {
        return self::groupsForLocaleStatic($locale);
    }

    /**
     * @param string $locale
     * @param string $group
     *
     * @return array
     */
    protected function loadGroup($locale, $group) {
        return require $this->i18nPath . '/' . $locale . '/' . $group . '.php';
    }

    public function testAtLeastTheKnownCoreLocalesExist() {
        $locales = $this->locales();

        foreach (['en_US', 'id_ID'] as $expected) {
            $this->assertContains($expected, $locales, "expected system/i18n/{$expected}/ to exist");
        }
    }

    public function testConfiguredFallbackLocaleHasATranslationFolder() {
        $fallback = CF::config('app.fallback_locale');
        $this->assertNotEmpty($fallback, 'app.fallback_locale must be configured');
        $this->assertContains(
            $fallback,
            $this->locales(),
            "app.fallback_locale is set to '{$fallback}', but system/i18n/{$fallback}/ doesn't exist -- "
            . 'every translation miss in every other locale silently falls back to a folder that has nothing in it'
        );
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public function provideEveryGroupFile() {
        $cases = [];
        foreach (self::localesStatic() as $locale) {
            foreach (self::groupsForLocaleStatic($locale) as $group) {
                $cases["{$locale}/{$group}"] = [$locale, $group];
            }
        }

        return $cases;
    }

    /**
     * PHPUnit data providers run before setUp(), so they're written as static
     * helpers here instead of relying on $this->i18nPath.
     *
     * @return string[]
     */
    protected static function localesStatic() {
        $i18nPath = rtrim(SYSPATH, '/') . '/i18n';
        $locales = [];
        foreach (scandir($i18nPath) as $entry) {
            if ($entry !== '.' && $entry !== '..' && is_dir($i18nPath . '/' . $entry)) {
                $locales[] = $entry;
            }
        }
        sort($locales);

        return $locales;
    }

    /**
     * @param string $locale
     *
     * @return string[]
     */
    protected static function groupsForLocaleStatic($locale) {
        $i18nPath = rtrim(SYSPATH, '/') . '/i18n';
        $base = $i18nPath . '/' . $locale;
        $groups = [];
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            /** @var SplFileInfo $file */
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $relative = substr($file->getPathname(), strlen($base) + 1);
            $groups[] = substr($relative, 0, -strlen('.php'));
        }
        sort($groups);

        return $groups;
    }

    /**
     * @dataProvider provideEveryGroupFile
     *
     * @param string $locale
     * @param string $group
     */
    public function testEveryGroupFileReturnsAnArrayOfStrings($locale, $group) {
        $result = $this->loadGroup($locale, $group);

        $this->assertIsArray($result, "system/i18n/{$locale}/{$group}.php must `return` an array (a file with no `return` statement evaluates to int(1) when required)");
        $this->assertLeavesAreStrings($result, "{$locale}/{$group}");
    }

    /**
     * Recursively verifies every leaf value in a (possibly nested, eg. Laravel-style
     * validation.php, the DataTables.js-shaped element/datatable.php, or a plain
     * numeric-indexed word list like captcha.php's `words`/`riddles`) translation
     * array is a string. Keys may be strings (a translation map) or ints (a plain
     * list of string variants/options) -- only leaf values are required to be strings.
     *
     * @param array  $array
     * @param string $label
     * @param string $path
     */
    protected function assertLeavesAreStrings(array $array, $label, $path = '') {
        foreach ($array as $key => $value) {
            $keyPath = $path === '' ? (string) $key : "{$path}.{$key}";
            if (is_array($value)) {
                $this->assertLeavesAreStrings($value, $label, $keyPath);
                continue;
            }
            $this->assertIsString($value, "{$label}['{$keyPath}'] must be a string (or a nested array of strings), got " . gettype($value));
        }
    }

    /**
     * @dataProvider provideEveryGroupFile
     *
     * @param string $locale
     * @param string $group
     */
    public function testNoGroupFileHasADuplicateArrayKeyWithinTheSameScope($locale, $group) {
        // Once PHP evaluates ['a' => 1, 'a' => 2], the duplicate is already
        // silently collapsed -- inspecting the loaded array can never catch
        // this, so scan the raw source instead. Several of these files (eg.
        // Laravel-style validation.php, or element/datatable.php mirroring
        // the DataTables.js i18n schema) legitimately reuse the same key name
        // in different nested sub-arrays ('array'/'file'/'numeric'/'string'
        // appearing under both 'min' and 'max', for instance) -- a duplicate
        // is only a real bug when it repeats within the SAME enclosing [ ].
        $path = $this->i18nPath . '/' . $locale . '/' . $group . '.php';
        $duplicates = $this->findDuplicateKeysInSameScope(file_get_contents($path));

        $this->assertEmpty(
            $duplicates,
            "system/i18n/{$locale}/{$group}.php declares the same key more than once in the same array: " . implode(', ', $duplicates)
        );
    }

    /**
     * @param string $source
     *
     * @return string[] duplicate 'key' => ... literals found within a single array scope
     */
    protected function findDuplicateKeysInSameScope($source) {
        $tokens = token_get_all($source);
        $scopeStack = [[]];
        $duplicates = [];
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $token = $tokens[$i];
            $text = is_array($token) ? $token[1] : $token;

            if ($text === '[') {
                $scopeStack[] = [];
                continue;
            }
            if ($text === ']') {
                array_pop($scopeStack);
                continue;
            }
            if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING) {
                // Look ahead past whitespace for '=>' to confirm this string literal is a key.
                $j = $i + 1;
                while ($j < $count && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
                    $j++;
                }
                $next = $tokens[$j] ?? null;
                $nextText = is_array($next) ? $next[1] : $next;
                if ($nextText === '=>') {
                    $key = trim($token[1], "'\"");
                    $currentScope = &$scopeStack[count($scopeStack) - 1];
                    if (isset($currentScope[$key])) {
                        $duplicates[] = $key;
                    }
                    $currentScope[$key] = true;
                    unset($currentScope);
                }
            }
        }

        return array_unique($duplicates);
    }

    /**
     * id_ID is allowed to be partially translated (missing groups/keys fall
     * back to app.fallback_locale) -- but for every key it DOES have, the
     * :placeholder tokens must match en_US's, or a translated string is
     * silently missing a variable substitution.
     */
    public function testTranslatedKeysPreserveTheSamePlaceholdersAsEnUs() {
        $mismatches = [];
        foreach ($this->groupsForLocale('id_ID') as $group) {
            $enUsPath = $this->i18nPath . '/en_US/' . $group . '.php';
            if (!file_exists($enUsPath)) {
                continue;
            }
            $idArr = $this->loadGroup('id_ID', $group);
            $enUsArr = require $enUsPath;
            if (!is_array($idArr) || !is_array($enUsArr)) {
                continue; // reported by testEveryGroupFileReturnsAnArrayOfStrings
            }

            $this->comparePlaceholders($idArr, $enUsArr, $group, $mismatches);
        }

        $this->assertEmpty($mismatches, "Placeholder mismatch between id_ID and en_US:\n" . implode("\n", $mismatches));
    }

    /**
     * @param array    $idArr
     * @param array    $enUsArr
     * @param string   $path
     * @param string[] $mismatches
     */
    protected function comparePlaceholders(array $idArr, array $enUsArr, $path, array &$mismatches) {
        foreach ($idArr as $key => $idValue) {
            if (!array_key_exists($key, $enUsArr)) {
                continue;
            }
            $keyPath = "{$path}.{$key}";
            $enUsValue = $enUsArr[$key];

            if (is_array($idValue) && is_array($enUsValue)) {
                $this->comparePlaceholders($idValue, $enUsValue, $keyPath, $mismatches);
                continue;
            }
            if (!is_string($idValue) || !is_string($enUsValue)) {
                continue; // reported by testEveryGroupFileReturnsAnArrayOfStrings
            }

            $idPlaceholders = $this->extractPlaceholders($idValue);
            $enUsPlaceholders = $this->extractPlaceholders($enUsValue);
            if ($idPlaceholders != $enUsPlaceholders) {
                $mismatches[] = "{$keyPath}: id_ID has [" . implode(',', $idPlaceholders) . '], en_US has [' . implode(',', $enUsPlaceholders) . ']';
            }
        }
    }

    /**
     * @param string $line
     *
     * @return string[] sorted, unique, lowercased :placeholder tokens found in the string
     */
    protected function extractPlaceholders($line) {
        preg_match_all('/:([a-zA-Z0-9_]+)/', $line, $matches);
        // CValidation_Trait_FormatMessageTrait replaces :attribute/:Attribute/:ATTRIBUTE
        // as case variants of the same placeholder (Laravel's convention), so
        // normalize case before comparing -- :Attribute in id_ID vs :attribute
        // in en_US is intentional, not a missing/renamed placeholder.
        $unique = array_unique(array_map('strtolower', $matches[1]));
        sort($unique);

        return $unique;
    }
}

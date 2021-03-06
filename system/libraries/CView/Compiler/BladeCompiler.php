<?php

/**
 * Description of BladeCompiler.
 *
 * @author Hery
 */
class CView_Compiler_BladeCompiler extends CView_CompilerAbstract implements CView_CompilerInterface {
    use CView_Compiler_BladeCompiler_CompileAuthorizationTrait,
        CView_Compiler_BladeCompiler_CompileClassesTrait,
        CView_Compiler_BladeCompiler_CompileCommentTrait,
        CView_Compiler_BladeCompiler_CompileComponentTrait,
        CView_Compiler_BladeCompiler_CompileConditionalTrait,
        CView_Compiler_BladeCompiler_CompileEchoTrait,
        CView_Compiler_BladeCompiler_CompileErrorTrait,
        CView_Compiler_BladeCompiler_CompileHelperTrait,
        CView_Compiler_BladeCompiler_CompileIncludeTrait,
        CView_Compiler_BladeCompiler_CompileInjectionTrait,
        CView_Compiler_BladeCompiler_CompileJsonTrait,
        CView_Compiler_BladeCompiler_CompileLayoutTrait,
        CView_Compiler_BladeCompiler_CompileLoopTrait,
        CView_Compiler_BladeCompiler_CompileRawPhpTrait,
        CView_Compiler_BladeCompiler_CompileStackTrait,
        CView_Compiler_BladeCompiler_CompileTranslationTrait;
    /*
      use Concerns\CompilesAuthorizations,
      Concerns\CompilesComments,
      Concerns\CompilesComponents,
      Concerns\CompilesConditionals,
      Concerns\CompilesEchos,
      Concerns\CompilesErrors,
      Concerns\CompilesHelpers,
      Concerns\CompilesIncludes,
      Concerns\CompilesInjections,
      Concerns\CompilesJson,
      Concerns\CompilesLayouts,
      Concerns\CompilesLoops,
      Concerns\CompilesRawPhp,
      Concerns\CompilesStacks,
      Concerns\CompilesTranslations;
     */

    /**
     * All of the registered extensions.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * All custom "directive" handlers.
     *
     * @var array
     */
    protected $customDirectives = [];

    /**
     * All custom "condition" handlers.
     *
     * @var array
     */
    protected $conditions = [];

    /**
     * All of the registered precompilers.
     *
     * @var array
     */
    protected $precompilers = [];

    /**
     * The file currently being compiled.
     *
     * @var string
     */
    protected $path;

    /**
     * All of the available compiler functions.
     *
     * @var string[]
     */
    protected $compilers = [
        // 'Comments',
        'Extensions',
        'Statements',
        'Echos',
    ];

    /**
     * Array of opening and closing tags for raw echos.
     *
     * @var string[]
     */
    protected $rawTags = ['{!!', '!!}'];

    /**
     * Array of opening and closing tags for regular echos.
     *
     * @var string[]
     */
    protected $contentTags = ['{{', '}}'];

    /**
     * Array of opening and closing tags for escaped echos.
     *
     * @var string[]
     */
    protected $escapedTags = ['{{{', '}}}'];

    /**
     * The "regular" / legacy echo string format.
     *
     * @var string
     */
    protected $echoFormat = 'c::e(%s)';

    /**
     * Array of footer lines to be added to template.
     *
     * @var array
     */
    protected $footer = [];

    /**
     * Array to temporary store the raw blocks found in the template.
     *
     * @var array
     */
    protected $rawBlocks = [];

    /**
     * The array of class component aliases and their class names.
     *
     * @var array
     */
    protected $classComponentAliases = [];

    /**
     * The array of class component namespaces to autoload from.
     *
     * @var array
     */
    protected $classComponentNamespaces = [];

    /**
     * Indicates if component tags should be compiled.
     *
     * @var bool
     */
    protected $compilesComponentTags = true;

    /**
     * @var CView_Compiler_BladeCompiler
     */
    private static $instance;

    /**
     * @return CView_Compiler_BladeCompiler
     */
    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Compile the view at the given path.
     *
     * @param null|string $path
     *
     * @return void
     */
    public function compile($path = null) {
        if ($path) {
            $this->setPath($path);
        }

        if (!is_null($this->cachePath)) {
            $contents = $this->compileString(CFile::get($this->getPath()));

            if (!empty($this->getPath())) {
                $contents = $this->appendFilePath($contents);
            }

            CFile::put(
                $this->getCompiledPath($this->getPath()),
                $contents
            );
        }
    }

    /**
     * Append the file path to the compiled string.
     *
     * @param string $contents
     *
     * @return string
     */
    protected function appendFilePath($contents) {
        $tokens = $this->getOpenAndClosingPhpTokens($contents);

        if ($tokens->isNotEmpty() && $tokens->last() !== T_CLOSE_TAG) {
            $contents .= ' ?>';
        }

        return $contents . "<?php /**PATH {$this->getPath()} ENDPATH**/ ?>";
    }

    /**
     * Get the open and closing PHP tag tokens from the given string.
     *
     * @param string $contents
     *
     * @return CCollection
     */
    protected function getOpenAndClosingPhpTokens($contents) {
        return c::collect(token_get_all($contents))
            ->pluck(0)
            ->filter(function ($token) {
                return in_array($token, [T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_CLOSE_TAG]);
            });
    }

    /**
     * Get the path currently being compiled.
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Set the path currently being compiled.
     *
     * @param string $path
     *
     * @return void
     */
    public function setPath($path) {
        $this->path = $path;
    }

    /**
     * Compile the given Blade template contents.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileString($value) {
        list($this->footer, $result) = [[], ''];

        // First we will compile the Blade component tags. This is a precompile style
        // step which compiles the component Blade tags into @component directives
        // that may be used by Blade. Then we should call any other precompilers.
        $value = $this->compileComponentTags(
            $this->compileComments($this->storeUncompiledBlocks($value))
        );

        foreach ($this->precompilers as $precompiler) {
            $value = call_user_func($precompiler, $value);
        }

        // Here we will loop through all of the tokens returned by the Zend lexer and
        // parse each one into the corresponding valid PHP. We will then have this
        // template as the correctly rendered PHP that can be rendered natively.
        foreach (token_get_all($value) as $token) {
            $result .= is_array($token) ? $this->parseToken($token) : $token;
        }

        if (!empty($this->rawBlocks)) {
            $result = $this->restoreRawContent($result);
        }

        // If there are any footer lines that need to get added to a template we will
        // add them here at the end of the template. This gets used mainly for the
        // template inheritance via the extends keyword that should be appended.
        if (count($this->footer) > 0) {
            $result = $this->addFooters($result);
        }

        return $result;
    }

    /**
     * Store the blocks that do not receive compilation.
     *
     * @param string $value
     *
     * @return string
     */
    protected function storeUncompiledBlocks($value) {
        if (strpos($value, '@verbatim') !== false) {
            $value = $this->storeVerbatimBlocks($value);
        }

        if (strpos($value, '@php') !== false) {
            $value = $this->storePhpBlocks($value);
        }

        return $value;
    }

    /**
     * Store the verbatim blocks and replace them with a temporary placeholder.
     *
     * @param string $value
     *
     * @return string
     */
    protected function storeVerbatimBlocks($value) {
        return preg_replace_callback('/(?<!@)@verbatim(.*?)@endverbatim/s', function ($matches) {
            return $this->storeRawBlock($matches[1]);
        }, $value);
    }

    /**
     * Store the PHP blocks and replace them with a temporary placeholder.
     *
     * @param string $value
     *
     * @return string
     */
    protected function storePhpBlocks($value) {
        return preg_replace_callback('/(?<!@)@php(.*?)@endphp/s', function ($matches) {
            return $this->storeRawBlock("<?php{$matches[1]}?>");
        }, $value);
    }

    /**
     * Store a raw block and return a unique raw placeholder.
     *
     * @param string $value
     *
     * @return string
     */
    protected function storeRawBlock($value) {
        return $this->getRawPlaceholder(
            array_push($this->rawBlocks, $value) - 1
        );
    }

    /**
     * Compile the component tags.
     *
     * @param string $value
     *
     * @return string
     */
    protected function compileComponentTags($value) {
        if (!$this->compilesComponentTags) {
            return $value;
        }

        return (new CView_Compiler_ComponentTagCompiler(
            $this->classComponentAliases,
            $this->classComponentNamespaces
        ))->compile($value);
    }

    /**
     * Replace the raw placeholders with the original code stored in the raw blocks.
     *
     * @param string $result
     *
     * @return string
     */
    protected function restoreRawContent($result) {
        $result = preg_replace_callback('/' . $this->getRawPlaceholder('(\d+)') . '/', function ($matches) {
            return $this->rawBlocks[$matches[1]];
        }, $result);

        $this->rawBlocks = [];

        return $result;
    }

    /**
     * Get a placeholder to temporary mark the position of raw blocks.
     *
     * @param int|string $replace
     *
     * @return string
     */
    protected function getRawPlaceholder($replace) {
        return str_replace('#', $replace, '@__raw_block_#__@');
    }

    /**
     * Add the stored footers onto the given content.
     *
     * @param string $result
     *
     * @return string
     */
    protected function addFooters($result) {
        return ltrim($result, "\n")
                . "\n" . implode("\n", array_reverse($this->footer));
    }

    /**
     * Parse the tokens from the template.
     *
     * @param array $token
     *
     * @return string
     */
    protected function parseToken($token) {
        list($id, $content) = $token;

        if ($id == T_INLINE_HTML) {
            foreach ($this->compilers as $type) {
                $content = $this->{"compile{$type}"}($content);
            }
        }

        return $content;
    }

    /**
     * Execute the user defined extensions.
     *
     * @param string $value
     *
     * @return string
     */
    protected function compileExtensions($value) {
        foreach ($this->extensions as $compiler) {
            $value = $compiler($value, $this);
        }

        return $value;
    }

    /**
     * Compile Blade statements that start with "@".
     *
     * @param string $value
     *
     * @return string
     */
    protected function compileStatements($value) {
        return preg_replace_callback(
            '/\B@(@?\w+(?:::\w+)?)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x',
            function ($match) {
                return $this->compileStatement($match);
            },
            $value
        );
    }

    /**
     * Compile a single Blade @ statement.
     *
     * @param array $match
     *
     * @return string
     */
    protected function compileStatement($match) {
        if (cstr::contains($match[1], '@')) {
            $match[0] = isset($match[3]) ? $match[1] . $match[3] : $match[1];
        } elseif (isset($this->customDirectives[$match[1]])) {
            $match[0] = $this->callCustomDirective($match[1], carr::get($match, 3));
        } elseif (method_exists($this, $method = 'compile' . ucfirst($match[1]))) {
            $match[0] = $this->$method(carr::get($match, 3));
        }

        return isset($match[3]) ? $match[0] : $match[0] . $match[2];
    }

    /**
     * Call the given directive with the given value.
     *
     * @param string      $name
     * @param null|string $value
     *
     * @return string
     */
    protected function callCustomDirective($name, $value) {
        if (cstr::startsWith($value, '(') && cstr::endsWith($value, ')')) {
            $value = cstr::substr($value, 1, -1);
        }

        return call_user_func($this->customDirectives[$name], trim($value));
    }

    /**
     * Strip the parentheses from the given expression.
     *
     * @param string $expression
     *
     * @return string
     */
    public function stripParentheses($expression) {
        if (cstr::startsWith($expression, '(')) {
            $expression = substr($expression, 1, -1);
        }

        return $expression;
    }

    /**
     * Register a custom Blade compiler.
     *
     * @param callable $compiler
     *
     * @return void
     */
    public function extend(callable $compiler) {
        $this->extensions[] = $compiler;
    }

    /**
     * Get the extensions used by the compiler.
     *
     * @return array
     */
    public function getExtensions() {
        return $this->extensions;
    }

    /**
     * Register an "if" statement directive.
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return void
     */
    public function aliasIf($name, callable $callback) {
        $this->conditions[$name] = $callback;

        $this->directive($name, function ($expression) use ($name) {
            return $expression !== '' ? "<?php if (CView_Blade::check('{$name}', {$expression})): ?>" : "<?php if (CView_Blade::check('{$name}')): ?>";
        });

        $this->directive('unless' . $name, function ($expression) use ($name) {
            return $expression !== '' ? "<?php if (! CView_Blade::check('{$name}', {$expression})): ?>" : "<?php if (! CView_Blade::check('{$name}')): ?>";
        });

        $this->directive('else' . $name, function ($expression) use ($name) {
            return $expression !== '' ? "<?php elseif (\CView_Blade::check('{$name}', {$expression})): ?>" : "<?php elseif (\CView_Blade::check('{$name}')): ?>";
        });

        $this->directive('end' . $name, function () {
            return '<?php endif; ?>';
        });
    }

    /**
     * Check the result of a condition.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return bool
     */
    public function check($name, ...$parameters) {
        return call_user_func($this->conditions[$name], ...$parameters);
    }

    /**
     * Register a class-based component alias directive.
     *
     * @param string      $class
     * @param null|string $alias
     * @param string      $prefix
     *
     * @return void
     */
    public function component($class, $alias = null, $prefix = '') {
        if (!is_null($alias) && cstr::contains($alias, '\\')) {
            list($class, $alias) = [$alias, $class];
        }

        if (is_null($alias)) {
            $alias = cstr::contains($class, '\\View\\Components\\') ? c::collect(explode('\\', cstr::after($class, '\\View\\Components\\')))->map(function ($segment) {
                return cstr::kebab($segment);
            })->implode(':') : cstr::kebab(c::classBasename($class));
        }

        if (!empty($prefix)) {
            $alias = $prefix . '-' . $alias;
        }

        $this->classComponentAliases[$alias] = $class;
    }

    /**
     * Register an array of class-based components.
     *
     * @param array  $components
     * @param string $prefix
     *
     * @return void
     */
    public function components(array $components, $prefix = '') {
        foreach ($components as $key => $value) {
            if (is_numeric($key)) {
                $this->component($value, null, $prefix);
            } else {
                $this->component($key, $value, $prefix);
            }
        }
    }

    /**
     * Get the registered class component aliases.
     *
     * @return array
     */
    public function getClassComponentAliases() {
        return $this->classComponentAliases;
    }

    /**
     * Register a class-based component namespace.
     *
     * @param string $namespace
     * @param string $prefix
     *
     * @return void
     */
    public function componentNamespace($namespace, $prefix) {
        $this->classComponentNamespaces[$prefix] = $namespace;
    }

    /**
     * Get the registered class component namespaces.
     *
     * @return array
     */
    public function getClassComponentNamespaces() {
        return $this->classComponentNamespaces;
    }

    /**
     * Register a component alias directive.
     *
     * @param string      $path
     * @param null|string $alias
     *
     * @return void
     */
    public function aliasComponent($path, $alias = null) {
        $alias = $alias ?: carr::last(explode('.', $path));

        $this->directive($alias, function ($expression) use ($path) {
            return $expression ? "<?php \$__env->startComponent('{$path}', {$expression}); ?>" : "<?php \$__env->startComponent('{$path}'); ?>";
        });

        $this->directive('end' . $alias, function ($expression) {
            return '<?php echo $__env->renderComponent(); ?>';
        });
    }

    /**
     * Register an include alias directive.
     *
     * @param string      $path
     * @param null|string $alias
     *
     * @return void
     */
    public function aliasInclude($path, $alias = null) {
        $alias = $alias ?: carr::last(explode('.', $path));

        $this->directive($alias, function ($expression) use ($path) {
            $expression = $this->stripParentheses($expression) ?: '[]';

            return "<?php echo \$__env->make('{$path}', {$expression}, \Illuminate\Support\carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
        });
    }

    /**
     * Register a handler for custom directives.
     *
     * @param string   $name
     * @param callable $handler
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function directive($name, callable $handler) {
        if (!preg_match('/^\w+(?:::\w+)?$/x', $name)) {
            throw new InvalidArgumentException("The directive name [{$name}] is not valid. Directive names must only contain alphanumeric characters and underscores.");
        }

        $this->customDirectives[$name] = $handler;
    }

    /**
     * Get the list of custom directives.
     *
     * @return array
     */
    public function getCustomDirectives() {
        return $this->customDirectives;
    }

    /**
     * Register a new precompiler.
     *
     * @param callable $precompiler
     *
     * @return void
     */
    public function precompiler(callable $precompiler) {
        $this->precompilers[] = $precompiler;
    }

    /**
     * Set the echo format to be used by the compiler.
     *
     * @param string $format
     *
     * @return void
     */
    public function setEchoFormat($format) {
        $this->echoFormat = $format;
    }

    /**
     * Set the "echo" format to double encode entities.
     *
     * @return void
     */
    public function withDoubleEncoding() {
        $this->setEchoFormat('c::e(%s, true)');
    }

    /**
     * Set the "echo" format to not double encode entities.
     *
     * @return void
     */
    public function withoutDoubleEncoding() {
        $this->setEchoFormat('c::e(%s, false)');
    }

    /**
     * Indicate that component tags should not be compiled.
     *
     * @return void
     */
    public function withoutComponentTags() {
        $this->compilesComponentTags = false;
    }

    public function clearCompiled() {
        //$path = CView_Factory::compiledPath();
        $path = CF::config('view.compiled');
        $path = rtrim($path, '/');
        if (CF::appCode()) {
            $path .= DIRECTORY_SEPARATOR . CF::appCode();
        }
        $files = glob($path . '/*');

        CFile::delete($files);
    }
}

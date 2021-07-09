<?php

/**
 * Description of ComponentTagCompiler
 *
 * @author Hery
 * @author Spatie bvba <info@spatie.be>
 * @author Taylor Otwell <taylor@laravel.com>
 */
class CView_Compiler_ComponentTagCompiler {
    /**
     * The Blade compiler instance.
     *
     * @var CView_Compiler_BladeCompiler
     */
    protected $blade;

    /**
     * The component class aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * The component class namespaces.
     *
     * @var array
     */
    protected $namespaces = [];

    /**
     * The "bind:" attributes that have been compiled for the current component.
     *
     * @var array
     */
    protected $boundAttributes = [];

    /**
     * Create new component tag compiler.
     *
     * @param array $aliases
     * @param  \Illuminate\View\Compilers\BladeCompiler|null
     *
     * @return void
     */
    public function __construct(array $aliases = [], array $namespaces = [], CView_Compiler_BladeCompiler $blade = null) {
        $this->aliases = $aliases;
        $this->namespaces = $namespaces;

        $this->blade = $blade != null ? $blade : new CView_Compiler_BladeCompiler();
    }

    /**
     * Compile the component and slot tags within the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public function compile($value) {
        $value = $this->compileSlots($value);

        return $this->compileTags($value);
    }

    /**
     * Compile the tags within the given string.
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function compileTags($value) {
        $value = $this->compileSelfClosingTags($value);
        $value = $this->compileOpeningTags($value);
        $value = $this->compileClosingTags($value);

        return $value;
    }

    /**
     * Compile the opening tags within the given string.
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function compileOpeningTags($value) {
        $pattern = "/
            <
                \s*
                cf[-\:]([\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
                (?<![\/=\-])
            >
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            return $this->componentString($matches[1], $attributes);
        }, $value);
    }

    /**
     * Compile the self-closing tags within the given string.
     *
     * @param string $value
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function compileSelfClosingTags($value) {
        $pattern = "/
            <
                \s*
                cf[-\:]([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                [\w\-:.@]+
                                (
                                    =
                                    (?:
                                        \\\"[^\\\"]*\\\"
                                        |
                                        \'[^\']*\'
                                        |
                                        [^\'\\\"=<>]+
                                    )
                                )?
                            )
                        )
                    )*
                    \s*
                )
            \/>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            return $this->componentString($matches[1], $attributes) . "\n@endcomponentClass ";
        }, $value);
    }

    /**
     * Compile the Blade component string for the given component and attributes.
     *
     * @param string $component
     * @param array  $attributes
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function componentString($component, array $attributes) {
        $class = $this->componentClass($component);

        list($data, $attributes) = $this->partitionDataAndAttributes($class, $attributes);

        $data = $data->mapWithKeys(function ($value, $key) {
            return [cstr::camel($key) => $value];
        });

        // If the component doesn't exists as a class we'll assume it's a class-less
        // component and pass the component as a view parameter to the data so it
        // can be accessed within the component and we can render out the view.
        if (!class_exists($class)) {
            $parameters = [
                'view' => "'$class'",
                'data' => '[' . $this->attributesToString($data->all(), $escapeBound = false) . ']',
            ];

            $class = '\\' . CView_Component_AnonymousComponent::class;
        } else {
            $parameters = $data->all();
        }

        return " @component('{$class}', '{$component}', [" . $this->attributesToString($parameters, $escapeBound = false) . '])
<?php $component->withAttributes([' . $this->attributesToString($attributes->all(), $escapeAttributes = $class !== CView_Component_DynamicComponent::class) . ']); ?>';
    }

    /**
     * Get the component class for a given component alias.
     *
     * @param string $component
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function componentClass($component) {
        $viewFactory = CContainer::getInstance()->make(CView_Factory::class);

        if (isset($this->aliases[$component])) {
            if (class_exists($alias = $this->aliases[$component])) {
                return $alias;
            }

            if ($viewFactory->exists($alias)) {
                return $alias;
            }

            throw new InvalidArgumentException(
                "Unable to locate class or view [{$alias}] for component [{$component}]."
            );
        }

        if ($class = $this->findClassByComponent($component)) {
            return $class;
        }

        if (class_exists($class = $this->guessClassName($component))) {
            return $class;
        }

        if ($viewFactory->exists($view = $this->guessViewName($component))) {
            return $view;
        }

        throw new InvalidArgumentException(
            "Unable to locate a class or view for component [{$component}]."
        );
    }

    /**
     * Find the class for the given component using the registered namespaces.
     *
     * @param string $component
     *
     * @return string|null
     */
    public function findClassByComponent($component) {
        $segments = explode('::', $component);

        $prefix = $segments[0];

        if (!isset($this->namespaces[$prefix]) || !isset($segments[1])) {
            return;
        }

        if (class_exists($class = $this->namespaces[$prefix] . '\\' . $this->formatClassName($segments[1]))) {
            return $class;
        }
    }

    /**
     * Guess the class name for the given component.
     *
     * @param string $component
     *
     * @return string
     */
    public function guessClassName($component) {
        $namespace = '';
        /*
        $namespace = CContainer::getInstance()
                ->make(Application::class)
                ->getNamespace();
        */
        $class = $this->formatClassName($component);

        return $namespace . 'View\\Components\\' . $class;
    }

    /**
     * Format the class name for the given component.
     *
     * @param string $component
     *
     * @return string
     */
    public function formatClassName($component) {
        $componentPieces = array_map(function ($componentPiece) {
            return ucfirst(cstr::camel($componentPiece));
        }, explode('.', $component));

        return implode('\\', $componentPieces);
    }

    /**
     * Guess the view name for the given component.
     *
     * @param string $name
     *
     * @return string
     */
    public function guessViewName($name) {
        $prefix = 'component.';

        $delimiter = CView::HINT_PATH_DELIMITER;

        if (cstr::contains($name, $delimiter)) {
            return cstr::replaceFirst($delimiter, $delimiter . $prefix, $name);
        }

        return $prefix . $name;
    }

    /**
     * Partition the data and extra attributes from the given array of attributes.
     *
     * @param string $class
     * @param array  $attributes
     *
     * @return array
     */
    public function partitionDataAndAttributes($class, array $attributes) {
        // If the class doesn't exists, we'll assume it's a class-less component and
        // return all of the attributes as both data and attributes since we have
        // now way to partition them. The user can exclude attributes manually.
        if (!class_exists($class)) {
            return [c::collect($attributes), c::collect($attributes)];
        }

        $constructor = (new ReflectionClass($class))->getConstructor();

        $parameterNames = $constructor ? c::collect($constructor->getParameters())->map->getName()->all() : [];

        return c::collect($attributes)->partition(function ($value, $key) use ($parameterNames) {
            return in_array(cstr::camel($key), $parameterNames);
        })->all();
    }

    /**
     * Compile the closing tags within the given string.
     *
     * @param string $value
     *
     * @return string
     */
    protected function compileClosingTags($value) {
        return preg_replace("/<\/\s*cf[-\:][\w\-\:\.]*\s*>/", ' @endcomponentClass ', $value);
    }

    /**
     * Compile the slot tags within the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileSlots($value) {
        $value = preg_replace_callback('/<\s*cf[\-\:]slot\s+(:?)name=(?<name>(\"[^\"]+\"|\\\'[^\\\']+\\\'|[^\s>]+))\s*>/', function ($matches) {
            $name = $this->stripQuotes($matches['name']);

            if ($matches[1] !== ':') {
                $name = "'{$name}'";
            }

            return " @slot({$name}) ";
        }, $value);

        return preg_replace('/<\/\s*cf[\-\:]slot[^>]*>/', ' @endslot', $value);
    }

    /**
     * Get an array of attributes from the given attribute string.
     *
     * @param string $attributeString
     *
     * @return array
     */
    protected function getAttributesFromAttributeString($attributeString) {
        $attributeString = $this->parseAttributeBag($attributeString);

        $attributeString = $this->parseBindAttributes($attributeString);

        $pattern = '/
            (?<attribute>[\w\-:.@]+)
            (
                =
                (?<value>
                    (
                        \"[^\"]+\"
                        |
                        \\\'[^\\\']+\\\'
                        |
                        [^\s>]+
                    )
                )
            )?
        /x';

        if (!preg_match_all($pattern, $attributeString, $matches, PREG_SET_ORDER)) {
            return [];
        }

        return c::collect($matches)->mapWithKeys(function ($match) {
            $attribute = $match['attribute'];
            $value = carr::get($match, 'value', null);

            if (is_null($value)) {
                $value = 'true';

                $attribute = cstr::start($attribute, 'bind:');
            }

            $value = $this->stripQuotes($value);

            if (cstr::startsWith($attribute, 'bind:')) {
                $attribute = cstr::after($attribute, 'bind:');

                $this->boundAttributes[$attribute] = true;
            } else {
                $value = "'" . $this->compileAttributeEchos($value) . "'";
            }

            return [$attribute => $value];
        })->toArray();
    }

    /**
     * Parse the attribute bag in a given attribute string into it's fully-qualified syntax.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function parseAttributeBag($attributeString) {
        $pattern = "/
            (?:^|\s+)                                        # start of the string or whitespace between attributes
            \{\{\s*(\\\$attributes(?:[^}]+?(?<!\s))?)\s*\}\} # exact match of attributes variable being echoed
        /x";

        return preg_replace($pattern, ' :attributes="$1"', $attributeString);
    }

    /**
     * Parse the "bind" attributes in a given attribute string into their fully-qualified syntax.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function parseBindAttributes($attributeString) {
        $pattern = "/
            (?:^|\s+)     # start of the string or whitespace between attributes
            :             # attribute needs to start with a semicolon
            ([\w\-:.@]+)  # match the actual attribute name
            =             # only match attributes that have a value
        /xm";

        return preg_replace($pattern, ' bind:$1=', $attributeString);
    }

    /**
     * Compile any Blade echo statements that are present in the attribute string.
     *
     * These echo statements need to be converted to string concatenation statements.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function compileAttributeEchos($attributeString) {
        $value = $this->blade->compileEchos($attributeString);

        $value = $this->escapeSingleQuotesOutsideOfPhpBlocks($value);

        $value = str_replace('<?php echo ', '\'.', $value);
        $value = str_replace('; ?>', '.\'', $value);

        return $value;
    }

    /**
     * Escape the single quotes in the given string that are outside of PHP blocks.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeSingleQuotesOutsideOfPhpBlocks($value) {
        return c::collect(token_get_all($value))->map(function ($token) {
            if (!is_array($token)) {
                return $token;
            }

            return $token[0] === T_INLINE_HTML ? str_replace("'", "\\'", $token[1]) : $token[1];
        })->implode('');
    }

    /**
     * Convert an array of attributes to a string.
     *
     * @param array $attributes
     * @param bool  $escapeBound
     *
     * @return string
     */
    protected function attributesToString(array $attributes, $escapeBound = true) {
        return c::collect($attributes)
            ->map(function ($value, $attribute) use ($escapeBound) {
                return $escapeBound && isset($this->boundAttributes[$attribute]) && $value !== 'true' && !is_numeric($value) ? "'{$attribute}' => CView_Compiler_BladeCompiler::sanitizeComponentAttribute({$value})" : "'{$attribute}' => {$value}";
            })
            ->implode(',');
    }

    /**
     * Strip any quotes from the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public function stripQuotes($value) {
        return cstr::startsWith($value, ['"', '\'']) ? substr($value, 1, -1) : $value;
    }
}

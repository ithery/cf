<?php

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
     * @param array $namespaces
     *
     * @return void
     */
    public function __construct(array $aliases = [], array $namespaces = []) {
        $this->aliases = $aliases;
        $this->namespaces = $namespaces;

        $this->blade = CView::blade();
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
     * @throws \InvalidArgumentException
     *
     * @return string
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
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function compileOpeningTags($value) {
        $pattern = "/
            <
                \s*
                x[-\:]([\w\-\:\.]*)
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
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
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    protected function compileSelfClosingTags($value) {
        $pattern = "/
            <
                \s*
                x[-\:]([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                \{\{\s*\\\$attributes(?:[^}]+?)?\s*\}\}
                            )
                            |
                            (?:
                                (\:\\\$)(\w+)
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

            return $this->componentString($matches[1], $attributes) . "\n@endComponentClass##END-COMPONENT-CLASS##";
        }, $value);
    }

    /**
     * Compile the Blade component string for the given component and attributes.
     *
     * @param string $component
     * @param array  $attributes
     *
     * @throws \InvalidArgumentException
     *
     * @return string
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
            $view = cstr::startsWith($component, 'mail::')
                ? "CView::factory()->make('{$component}')"
                : "'$class'";

            $parameters = [
                'view' => $view,
                'data' => '[' . $this->attributesToString($data->all(), $escapeBound = false) . ']',
            ];

            $class = '\\' . CView_Component_AnonymousComponent::class;
        } else {
            $parameters = $data->all();
        }

        return "##BEGIN-COMPONENT-CLASS##@component('{$class}', '{$component}', [" . $this->attributesToString($parameters, $escapeBound = false) . '])
<?php if (isset($attributes) && $attributes instanceof CView_ComponentAttributeBag && $constructor = (new ReflectionClass(' . $class . '::class))->getConstructor()): ?>
<?php $attributes = $attributes->except(c::collect($constructor->getParameters())->map->getName()->all()); ?>
<?php endif; ?>
<?php $component->withAttributes([' . $this->attributesToString($attributes->all(), $escapeAttributes = $class !== CView_Component_DynamicComponent::class) . ']); ?>';
    }

    /**
     * Get the component class for a given component alias.
     *
     * @param string $component
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function componentClass($component) {
        $viewFactory = CView::factory();
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
        if (!is_null($guess = $this->guessAnonymousComponentUsingNamespaces($viewFactory, $component))
            || !is_null($guess = $this->guessAnonymousComponentUsingPaths($viewFactory, $component))
        ) {
            return $guess;
        }

        if (cstr::startsWith($component, 'mail::')) {
            return $component;
        }

        throw new InvalidArgumentException(
            "Unable to locate a class or view for component [{$component}]."
        );
    }

    /**
     * Attempt to find an anonymous component using the registered anonymous component paths.
     *
     * @param \CView_Factory $viewFactory
     * @param string         $component
     *
     * @return null|string
     */
    protected function guessAnonymousComponentUsingPaths(CView_Factory $viewFactory, string $component) {
        $delimiter = CView::HINT_PATH_DELIMITER;

        foreach ($this->blade->getAnonymousComponentPaths() as $path) {
            try {
                if (cstr::contains($component, $delimiter)
                    && !cstr::startsWith($component, $path['prefix'] . $delimiter)
                ) {
                    continue;
                }

                $formattedComponent = cstr::startsWith($component, $path['prefix'] . $delimiter)
                        ? cstr::after($component, $delimiter)
                        : $component;

                if ($viewFactory->exists($guess = $path['prefixHash'] . $delimiter . $formattedComponent)) {
                    return $guess;
                }
                if ($viewFactory->exists($guess = $path['prefixHash'] . $delimiter . $formattedComponent . '.index')) {
                    return $guess;
                }
            } catch (InvalidArgumentException $e) {
            }
        }
    }

    /**
     * Attempt to find an anonymous component using the registered anonymous component namespaces.
     *
     * @param \CView_Factory $viewFactory
     * @param string         $component
     *
     * @return null|string
     */
    protected function guessAnonymousComponentUsingNamespaces(CView_Factory $viewFactory, string $component) {
        return c::collect($this->blade->getAnonymousComponentNamespaces())
            ->filter(function ($directory, $prefix) use ($component) {
                return cstr::startsWith($component, $prefix . '::');
            })
            ->prepend('components', $component)
            ->reduce(function ($carry, $directory, $prefix) use ($component, $viewFactory) {
                if (!is_null($carry)) {
                    return $carry;
                }

                $componentName = cstr::after($component, $prefix . '::');

                if ($viewFactory->exists($view = $this->guessViewName($componentName, $directory))) {
                    return $view;
                }

                if ($viewFactory->exists($view = $this->guessViewName($componentName, $directory) . '.index')) {
                    return $view;
                }
            });
    }

    /**
     * Find the class for the given component using the registered namespaces.
     *
     * @param string $component
     *
     * @return null|string
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
        $namespace = '\\';
        /*
        $namespace = CContainer::getInstance()
                ->make(Application::class)
                ->getNamespace();
        */

        $class = $this->formatClassName($component);

        return $namespace . 'CView_Component_' . $class . 'Component';
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
     * @param string $prefix
     *
     * @return string
     */
    public function guessViewName($name, $prefix = 'component.') {
        if (!cstr::endsWith($prefix, '.')) {
            $prefix .= '.';
        }

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
        return preg_replace("/<\/\s*x[-\:][\w\-\:\.]*\s*>/", ' @endComponentClass##END-COMPONENT-CLASS##', $value);
    }

    /**
     * Compile the slot tags within the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public function compileSlots($value) {
        $pattern = "/
            <
                \s*
                x[\-\:]slot
                (?:\:(?<inlineName>\w+(?:-\w+)*))?
                (?:\s+name=(?<name>(\"[^\"]+\"|\\\'[^\\\']+\\\'|[^\s>]+)))?
                (?:\s+\:name=(?<boundName>(\"[^\"]+\"|\\\'[^\\\']+\\\'|[^\s>]+)))?
                (?<attributes>
                    (?:
                        \s+
                        (?:
                            (?:
                                @(?:class)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
                            (?:
                                @(?:style)(\( (?: (?>[^()]+) | (?-1) )* \))
                            )
                            |
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

        $value = preg_replace_callback($pattern, function ($matches) {
            $name = $this->stripQuotes($matches['inlineName'] ?: $matches['name'] ?: $matches['boundName']);

            if (cstr::contains($name, '-') && !empty($matches['inlineName'])) {
                $name = cstr::camel($name);
            }

            // If the name was given as a simple string, we will wrap it in quotes as if it was bound for convenience...
            if (!empty($matches['inlineName']) || !empty($matches['name'])) {
                $name = "'{$name}'";
            }

            $this->boundAttributes = [];

            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            // If an inline name was provided and a name or bound name was *also* provided, we will assume the name should be an attribute...
            if (!empty($matches['inlineName']) && (!empty($matches['name']) || !empty($matches['boundName']))) {
                $attributes = !empty($matches['name'])
                    ? array_merge($attributes, $this->getAttributesFromAttributeString('name=' . $matches['name']))
                    : array_merge($attributes, $this->getAttributesFromAttributeString(':name=' . $matches['boundName']));
            }

            return " @slot({$name}, null, [" . $this->attributesToString($attributes) . ']) ';
        }, $value);

        return preg_replace('/<\/\s*x[\-\:]slot[^>]*>/', ' @endslot', $value);
    }

    /**
     * Get an array of attributes from the given attribute string.
     *
     * @param string $attributeString
     *
     * @return array
     */
    protected function getAttributesFromAttributeString($attributeString) {
        $attributeString = $this->parseShortAttributeSyntax($attributeString);
        $attributeString = $this->parseAttributeBag($attributeString);
        $attributeString = $this->parseComponentTagClassStatements($attributeString);
        $attributeString = $this->parseComponentTagStyleStatements($attributeString);
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
            $value = $match['value'] ?? null;

            if (is_null($value)) {
                $value = 'true';

                $attribute = cstr::start($attribute, 'bind:');
            }

            $value = $this->stripQuotes($value);

            if (str_starts_with($attribute, 'bind:')) {
                $attribute = cstr::after($attribute, 'bind:');

                $this->boundAttributes[$attribute] = true;
            } else {
                $value = "'" . $this->compileAttributeEchos($value) . "'";
            }

            if (str_starts_with($attribute, '::')) {
                $attribute = substr($attribute, 1);
            }

            return [$attribute => $value];
        })->toArray();
    }

    /**
     * Parses a short attribute syntax like :$foo into a fully-qualified syntax like :foo="$foo".
     *
     * @param string $value
     *
     * @return string
     */
    protected function parseShortAttributeSyntax(string $value) {
        $pattern = "/\s\:\\\$(\w+)/x";

        return preg_replace_callback($pattern, function (array $matches) {
            return " :{$matches[1]}=\"\${$matches[1]}\"";
        }, $value);
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
     * Parse @class statements in a given attribute string into their fully-qualified syntax.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function parseComponentTagClassStatements(string $attributeString) {
        return preg_replace_callback(
            '/@(class)(\( ( (?>[^()]+) | (?2) )* \))/x',
            function ($match) {
                if ($match[1] === 'class') {
                    $match[2] = str_replace('"', "'", $match[2]);

                    return ":class=\"\carr::toCssClasses{$match[2]}\"";
                }

                return $match[0];
            },
            $attributeString
        );
    }

    /**
     * Parse @style statements in a given attribute string into their fully-qualified syntax.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function parseComponentTagStyleStatements(string $attributeString) {
        return preg_replace_callback(
            '/@(style)(\( ( (?>[^()]+) | (?2) )* \))/x',
            function ($match) {
                if ($match[1] === 'style') {
                    $match[2] = str_replace('"', "'", $match[2]);

                    return ":style=\"\carr::toCssStyles{$match[2]}\"";
                }

                return $match[0];
            },
            $attributeString
        );
    }

    /**
     * Parse the "bind" attributes in a given attribute string into their fully-qualified syntax.
     *
     * @param string $attributeString
     *
     * @return string
     */
    protected function parseBindAttributes(string $attributeString) {
        $pattern = "/
            (?:^|\s+)     # start of the string or whitespace between attributes
            :(?!:)        # attribute needs to start with a single colon
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

            return $token[0] === T_INLINE_HTML
                ? str_replace("'", "\\'", $token[1])
                : $token[1];
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
                return $escapeBound && isset($this->boundAttributes[$attribute]) && $value !== 'true' && !is_numeric($value)
                    ? "'{$attribute}' => \CView_Compiler_BladeCompiler::sanitizeComponentAttribute({$value})"
                    : "'{$attribute}' => {$value}";
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

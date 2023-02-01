<?php

defined('SYSPATH') or die('No direct access allowed.');

class CComponent_ComponentTagCompiler extends CView_Compiler_ComponentTagCompiler {
    public function compile($value) {
        return $this->compileComponentSelfClosingTags($value);
    }

    protected function compileComponentSelfClosingTags($value) {
        $pattern = "/
            <
                \s*
                CAppComponent\:([\w\-\:\.]*)
                \s*
                (?<attributes>
                    (?:
                        \s+
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
                    )*
                    \s*
                )
            \/?>
        /x";

        return preg_replace_callback($pattern, function (array $matches) {
            $attributes = $this->getAttributesFromAttributeString($matches['attributes']);

            // Convert kebab attributes to camel-case.
            $attributes = c::collect($attributes)->mapWithKeys(function ($value, $key) {
                return [(string) c::str($key)->camel() => $value];
            })->toArray();

            return $this->componentString($matches[1], $attributes);
        }, $value);
    }

    protected function componentString($component, array $attributes) {
        if (isset($attributes['key'])) {
            $key = $attributes['key'];
            unset($attributes['key']);

            return "@CAppComponent('{$component}', [" . $this->attributesToString($attributes, $escapeBound = false) . "], key({$key}))";
        }

        return "@CAppComponent('{$component}', [" . $this->attributesToString($attributes, $escapeBound = false) . '])';
    }
}

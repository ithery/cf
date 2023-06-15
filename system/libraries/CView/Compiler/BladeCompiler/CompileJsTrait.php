<?php

trait CView_Compiler_BladeCompiler_CompileJsTrait {
    /**
     * Compile the "@js" directive into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileJs(string $expression) {
        return sprintf(
            "<?php echo \%s::from(%s)->toHtml() ?>",
            CBase_Js::class,
            $this->stripParentheses($expression)
        );
    }
}

<?php

trait CView_Compiler_BladeCompiler_CompileStyleTrait {
    /**
     * Compile the conditional style statement into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileStyle($expression) {
        $expression = is_null($expression) ? '([])' : $expression;

        return "style=\"<?php echo \carr::toCssStyles{$expression} ?>\"";
    }
}

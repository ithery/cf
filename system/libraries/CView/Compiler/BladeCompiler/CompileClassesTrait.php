<?php

trait CView_Compiler_BladeCompiler_CompileClassesTrait {
    /**
     * Compile the conditional class statement into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileClass($expression) {
        $expression = is_null($expression) ? '([])' : $expression;

        return "class=\"<?php echo \carr::toCssClasses{$expression} ?>\"";
    }
}

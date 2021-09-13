<?php

class CTemplate_Compiler_BladeCompiler extends CView_Compiler_BladeCompiler {
    /**
     * Compile the include statements into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileInclude($expression) {
        $expression = $this->stripParentheses($expression);

        return "<?php echo \$__env->make({$expression}, \carr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>";
    }
}

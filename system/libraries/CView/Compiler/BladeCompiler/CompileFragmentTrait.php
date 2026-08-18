<?php

trait CView_Compiler_BladeCompiler_CompileFragmentTrait {
    /**
     * The last compiled fragment.
     *
     * @var string
     */
    protected $lastFragment;

    /**
     * Compile the fragment statements into valid PHP.
     *
     * @param string $expression
     *
     * @return string
     */
    protected function compileFragment($expression) {
        $this->lastFragment = trim($expression, "()'\" ");

        return "<?php \$__env->startFragment{$expression}; ?>";
    }

    /**
     * Compile the end-fragment statements into valid PHP.
     *
     * @return string
     */
    protected function compileEndfragment() {
        return '<?php echo $__env->stopFragment(); ?>';
    }
}

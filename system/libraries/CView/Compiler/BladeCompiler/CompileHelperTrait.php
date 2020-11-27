<?php

/**
 * Description of CompileHelperTrait
 *
 * @author Hery
 */
trait CView_Compiler_BladeCompiler_CompileHelperTrait {

    /**
     * Compile the CSRF statements into valid PHP.
     *
     * @return string
     */
    /*
    protected function compileCsrf() {
        return '<?php echo csrf_field(); ?>';
    }
    */
    /**
     * Compile the "dd" statements into valid PHP.
     *
     * @param  string  $arguments
     * @return string
     */
    protected function compileDd($arguments) {
        return "<?php cdbg::dd{$arguments}; ?>";
    }

    /**
     * Compile the "dump" statements into valid PHP.
     *
     * @param  string  $arguments
     * @return string
     */
    protected function compileDump($arguments) {
        return "<?php cdbg::d{$arguments}; ?>";
    }

    /**
     * Compile the method statements into valid PHP.
     *
     * @param  string  $method
     * @return string
     */
    /*
    protected function compileMethod($method) {
        return "<?php echo method_field{$method}; ?>";
    }
*/
}

<?php
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

class CDebug_Dumper {
    /**
     * Dump a value with elegance.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function dump($value) {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new CDebug_HtmlDumper();

            $dumper->dump((new VarCloner)->cloneVar($value));
        } else {
            var_dump($value);
        }
    }

    /**
     * Dump a value with elegance.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function getDump($value) {
        if (class_exists(CliDumper::class)) {
            $dumper = 'cli' === PHP_SAPI ? new CliDumper() : new CDebug_HtmlDumper();

            return $dumper->dump((new VarCloner)->cloneVar($value), true);
        } else {
            var_export($value, true);
        }
    }
}

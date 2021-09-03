<?php

use Symfony\Component\Console\Output\OutputInterface;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
interface CTesting_WriterInterface {
    /**
     * Ignores traces where the file string matches one
     * of the provided regex expressions.
     *
     * @param string[] $ignore the regex expressions
     *
     * @return CTesting_WriterInterface
     */
    public function ignoreFilesIn(array $ignore);

    /**
     * Declares whether or not the Writer should show the trace.
     *
     * @return CTesting_WriterInterface
     */
    public function showTrace(bool $show);

    /**
     * Declares whether or not the Writer should show the title.
     *
     * @return CTesting_WriterInterface
     */
    public function showTitle(bool $show);

    /**
     * Declares whether or not the Writer should show the editor.
     *
     * @return CTesting_WriterInterface
     */
    public function showEditor(bool $show);

    /**
     * Writes the details of the exception on the console.
     */
    public function write(Inspector $inspector);

    /**
     * Sets the output.
     *
     * @return CTesting_WriterInterface
     */
    public function setOutput(OutputInterface $output);

    /**
     * Gets the output.
     */
    public function getOutput();
}

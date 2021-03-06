<?php

interface CConsole_KernelInterface {
    /**
     * Handle an incoming console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface        $input
     * @param null|\Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function handle($input, $output = null);

    /**
     * Run an Artisan console command by name.
     *
     * @param string                                                 $command
     * @param array                                                  $parameters
     * @param null|\Symfony\Component\Console\Output\OutputInterface $outputBuffer
     *
     * @return int
     */
    public function call($command, array $parameters = [], $outputBuffer = null);

    /**
     * Queue an Artisan console command by name.
     *
     * @param string $command
     * @param array  $parameters
     *
     * @return \CQueue_PendingDispatch
     */
    public function queue($command, array $parameters = []);

    /**
     * Get all of the commands registered with the console.
     *
     * @return array
     */
    public function all();

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output();
}

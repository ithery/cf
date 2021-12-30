<?php

class CConsole_Command_EnvironmentCommand extends CConsole_Command {
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'env';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display the current framework environment';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle() {
        $this->line('<info>Current application environment:</info> <comment>' . CF::environment() . '</comment>');
    }
}

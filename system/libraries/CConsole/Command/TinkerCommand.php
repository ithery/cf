<?php

use Psy\Shell;
use Psy\Configuration;
use Psy\VersionUpdater\Checker;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CConsole_Command_TinkerCommand extends CConsole_Command {
    /**
     * Artisan commands to include in the tinker shell.
     *
     * @var array
     */
    protected $commandWhitelist = [
        'clear-compiled', 'down', 'env', 'inspire', 'migrate', 'migrate:install', 'optimize', 'up',
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'tinker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Interact with your application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->getApplication()->setCatchExceptions(false);

        $config = Configuration::fromInput($this->input);
        $config->setUpdateCheck(Checker::NEVER);

        $config->getPresenter()->addCasters(
            $this->getCasters()
        );

        $shell = new Shell($config);
        $shell->addCommands($this->getCommands());
        $shell->setIncludes($this->argument('include'));

        // $path = Env::get('COMPOSER_VENDOR_DIR', $this->getLaravel()->basePath() . DIRECTORY_SEPARATOR . 'vendor');

        // $path .= '/composer/autoload_classmap.php';

        //$config = $this->getLaravel()->make('config');

        // $loader = ClassAliasAutoloader::register(
        //     $shell,
        //     $path,
        //     $config->get('tinker.alias', []),
        //     $config->get('tinker.dont_alias', [])
        // );

        if ($code = $this->option('execute')) {
            try {
                $shell->setOutput($this->output);
                $shell->execute($code);
            } finally {
                //$loader->unregister();
            }

            return 0;
        }

        try {
            return $shell->run();
        } finally {
            //$loader->unregister();
        }
    }

    /**
     * Get artisan commands to pass through to PsySH.
     *
     * @return array
     */
    protected function getCommands() {
        $commands = [];

        // foreach ($this->getApplication()->all() as $name => $command) {
        //     if (in_array($name, $this->commandWhitelist)) {
        //         $commands[] = $command;
        //     }
        // }

        // $config = $this->getLaravel()->make('config');

        // foreach ($config->get('tinker.commands', []) as $command) {
        //     $commands[] = $this->getApplication()->add(
        //         $this->getLaravel()->make($command)
        //     );
        // }

        return $commands;
    }

    /**
     * Get an array of Laravel tailored casters.
     *
     * @return array
     */
    protected function getCasters() {
        $casters = [
            'CCollection' => 'CConsole_Tinker_TinkerCaster::castCollection',
            'CBase_HtmlString' => 'CConsole_Tinker_TinkerCaster::castHtmlString',
            'CBase_String' => 'CConsole_Tinker_TinkerCaster::castStringable',
            'CModel' => 'CConsole_Tinker_TinkerCaster::castModel',
        ];

        // if (class_exists('Illuminate\Foundation\Application')) {
        //     $casters['Illuminate\Foundation\Application'] = 'Laravel\Tinker\TinkerCaster::castApplication';
        // }

        return array_merge($casters, (array) CF::config('tinker.casters', []));
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments() {
        return [
            ['include', InputArgument::IS_ARRAY, 'Include file(s) before starting tinker'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions() {
        return [
            ['execute', null, InputOption::VALUE_OPTIONAL, 'Execute the given code using Tinker'],
        ];
    }
}

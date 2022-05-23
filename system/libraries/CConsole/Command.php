<?php

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class CConsole_Command extends SymfonyCommand {
    use CConsole_Trait_InteractsWithIOTrait;
    use CConsole_Trait_HasParameters;
    use CTrait_Macroable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description;

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Create a new console command instance.
     *
     * @return void
     */
    public function __construct() {
        // We will go ahead and set the name, description, and parameters on console
        // commands just to make things a little easier on the developer. This is
        // so they don't have to all be manually specified in the constructors.
        if (isset($this->signature)) {
            $this->configureUsingFluentDefinition();
        } else {
            parent::__construct($this->name);
        }

        // Once we have constructed the command, we'll set the description and other
        // related properties of the command. If a signature wasn't used to build
        // the command we'll set the arguments and the options on this command.
        $this->setDescription($this->description);

        $this->setHidden($this->hidden);

        if (!isset($this->signature)) {
            $this->specifyParameters();
        }
    }

    /**
     * Configure the console command using a fluent definition.
     *
     * @return void
     */
    protected function configureUsingFluentDefinition() {
        list($name, $arguments, $options) = CConsole_Parser::parse($this->signature);

        parent::__construct($this->name = $name);

        // After parsing the signature we will spin through the arguments and options
        // and set them on this command. These will already be changed into proper
        // instances of these "InputArgument" and "InputOption" Symfony classes.
        $this->getDefinition()->addArguments($arguments);
        $this->getDefinition()->addOptions($options);
    }

    /**
     * Run the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return int
     */
    public function run(InputInterface $input, OutputInterface $output) {
        return parent::run(
            $this->input = $input,
            $this->output = new CConsole_OutputStyle($input, $output)
        );
    }

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output) {
        return CContainer::getInstance()->call([$this, 'handle']);
    }

    /**
     * Call another console command.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function call($command, array $arguments = []) {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run(
            $this->createInputFromArguments($arguments),
            $this->output
        );
    }

    /**
     * Call another console command silently.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function callSilent($command, array $arguments = []) {
        $arguments['command'] = $command;

        return $this->getApplication()->find($command)->run(
            $this->createInputFromArguments($arguments),
            new NullOutput()
        );
    }

    /**
     * Create an input instance from the given arguments.
     *
     * @param array $arguments
     *
     * @return \Symfony\Component\Console\Input\ArrayInput
     */
    protected function createInputFromArguments(array $arguments) {
        return c::tap(new ArrayInput($arguments), function ($input) {
            if ($input->hasParameterOption(['--no-interaction'], true)) {
                $input->setInteractive(false);
            }
        });
    }
}

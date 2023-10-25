<?php

/**
 * @method void  alert(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method mixed ask(string $question, string $default = null)
 * @method mixed askWithCompletion(string $question, array|callable $choices, string $default = null)
 * @method void  bulletList(array $elements, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method mixed choice(string $question, array $choices, $default = null, int $attempts = null, bool $multiple = false)
 * @method bool  confirm(string $question, bool $default = false)
 * @method void  error(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void  info(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void  line(string $style, string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void  task(string $description, ?callable $task = null, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void  twoColumnDetail(string $first, ?string $second = null, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 * @method void  warn(string $string, int $verbosity = \Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL)
 */
class CConsole_View_ComponentFactory {
    /**
     * The output interface implementation.
     *
     * @var \CConsole_OutputStyle
     */
    protected $output;

    /**
     * Creates a new factory instance.
     *
     * @param \CConsole_OutputStyle $output
     *
     * @return void
     */
    public function __construct($output) {
        $this->output = $output;
    }

    /**
     * Dynamically handle calls into the component instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        $component = 'CConsole_View_Component_' . ucfirst($method);

        c::throwUnless(class_exists($component), new InvalidArgumentException(sprintf(
            'Console component [%s] not found.',
            $method
        )));

        return c::with(new $component($this->output))->render(...$parameters);
    }
}

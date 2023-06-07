<?php

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;

class CConsole_ContainerCommandLoader implements CommandLoaderInterface {
    /**
     * A map of command names to classes.
     *
     * @var array
     */
    protected $commandMap;

    /**
     * Create a new command loader instance.
     *
     * @param array $commandMap
     *
     * @return void
     */
    public function __construct(array $commandMap) {
        $this->commandMap = $commandMap;
    }

    /**
     * Resolve a command from the container.
     *
     * @param string $name
     *
     * @throws \Symfony\Component\Console\Exception\CommandNotFoundException
     *
     * @return \Symfony\Component\Console\Command\Command
     */
    public function get($name) {
        if (!$this->has($name)) {
            throw new CommandNotFoundException(sprintf('Command "%s" does not exist.', $name));
        }

        return CContainer::getInstance()->get($this->commandMap[$name]);
    }

    /**
     * Determines if a command exists.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name) {
        return $name && isset($this->commandMap[$name]);
    }

    /**
     * Get the command names.
     *
     * @return string[]
     */
    public function getNames() {
        return array_keys($this->commandMap);
    }
}

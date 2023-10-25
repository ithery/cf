<?php

interface CConsole_CommandMutexInterface {
    /**
     * Attempt to obtain a command mutex for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function create($command);

    /**
     * Determine if a command mutex exists for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function exists($command);

    /**
     * Release the mutex for the given command.
     *
     * @param \CConsole_Command $command
     *
     * @return bool
     */
    public function forget($command);
}

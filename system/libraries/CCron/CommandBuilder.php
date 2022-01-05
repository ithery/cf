<?php

class CCron_CommandBuilder {
    /**
     * Build the command for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return string
     */
    public function buildCommand(CCron_Event $event) {
        if ($event->runInBackground) {
            return $this->buildBackgroundCommand($event);
        }

        return $this->buildForegroundCommand($event);
    }

    /**
     * Build the command for running the event in the foreground.
     *
     * @param \CCron_Event $event
     *
     * @return string
     */
    protected function buildForegroundCommand(CCron_Event $event) {
        $output = CBase_ProcessUtils::escapeArgument($event->output);

        return $this->ensureCorrectUser(
            $event,
            $event->command . ($event->shouldAppendOutput ? ' >> ' : ' > ') . $output . ' 2>&1'
        );
    }

    /**
     * Build the command for running the event in the background.
     *
     * @param \CCron_Event $event
     *
     * @return string
     */
    protected function buildBackgroundCommand(CCron_Event $event) {
        $output = CBase_ProcessUtils::escapeArgument($event->output);

        $redirect = $event->shouldAppendOutput ? ' >> ' : ' > ';

        $finished = CConsole_Application::formatCommandString('schedule:finish') . ' "' . $event->mutexName() . '"';

        if (c::windowsOs()) {
            return 'start /b cmd /v:on /c "(' . $event->command . ' & ' . $finished . ' ^!ERRORLEVEL^!)' . $redirect . $output . ' 2>&1"';
        }

        return $this->ensureCorrectUser(
            $event,
            '(' . $event->command . $redirect . $output . ' 2>&1 ; ' . $finished . ' "$?") > '
            . CBase_ProcessUtils::escapeArgument($event->getDefaultOutput()) . ' 2>&1 &'
        );
    }

    /**
     * Finalize the event's command syntax with the correct user.
     *
     * @param \CCron_Event $event
     * @param string                   $command
     *
     * @return string
     */
    protected function ensureCorrectUser(CCron_Event $event, $command) {
        return $event->user && !c::windowsOs() ? 'sudo -u ' . $event->user . ' -- sh -c \'' . $command . '\'' : $command;
    }
}

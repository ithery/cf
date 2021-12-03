<?php

class CConsole_Schedule_CommandBuilder {
    /**
     * Build the command for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return string
     */
    public function buildCommand(CConsole_Schedule_Event $event) {
        if ($event->runInBackground) {
            return $this->buildBackgroundCommand($event);
        }

        return $this->buildForegroundCommand($event);
    }

    /**
     * Build the command for running the event in the foreground.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return string
     */
    protected function buildForegroundCommand(CConsole_Schedule_Event $event) {
        $output = CBase_ProcessUtils::escapeArgument($event->output);

        return $this->ensureCorrectUser(
            $event,
            $event->command . ($event->shouldAppendOutput ? ' >> ' : ' > ') . $output . ' 2>&1'
        );
    }

    /**
     * Build the command for running the event in the background.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return string
     */
    protected function buildBackgroundCommand(CConsole_Schedule_Event $event) {
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
     * @param \CConsole_Schedule_Event $event
     * @param string                   $command
     *
     * @return string
     */
    protected function ensureCorrectUser(CConsole_Schedule_Event $event, $command) {
        return $event->user && !c::windowsOs() ? 'sudo -u ' . $event->user . ' -- sh -c \'' . $command . '\'' : $command;
    }
}

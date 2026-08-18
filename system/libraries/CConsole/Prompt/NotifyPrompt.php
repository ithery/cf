<?php

class CConsole_Prompt_NotifyPrompt extends CConsole_Prompt {
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $body;

    /**
     * @var string
     */
    public $subtitle;

    /**
     * @var string
     */
    public $sound;

    /**
     * @var string
     */
    public $icon;

    /**
     * Create a new NotifyPrompt instance.
     *
     * @param string $title
     * @param string $body
     * @param string $subtitle
     * @param string $sound
     * @param string $icon
     */
    public function __construct($title, $body = '', $subtitle = '', $sound = '', $icon = '') {
        $this->title = $title;
        $this->body = $body;
        $this->subtitle = $subtitle;
        $this->sound = $sound;
        $this->icon = $icon;
    }

    /**
     * Send the notification.
     *
     * @return bool
     */
    public function prompt() {
        if (PHP_OS_FAMILY === 'Darwin') {
            return $this->sendMacOS();
        }

        if (PHP_OS_FAMILY === 'Linux') {
            return $this->sendLinux();
        }

        return false;
    }

    /**
     * Send a notification on macOS using osascript.
     *
     * @return bool
     */
    protected function sendMacOS() {
        $script = 'display notification ' . $this->escapeAppleScript($this->body);
        $script .= ' with title ' . $this->escapeAppleScript($this->title);

        if ($this->subtitle !== '') {
            $script .= ' subtitle ' . $this->escapeAppleScript($this->subtitle);
        }

        if ($this->sound !== '') {
            $script .= ' sound name ' . $this->escapeAppleScript($this->sound);
        }

        return $this->execute(['osascript', '-e', $script]);
    }

    /**
     * Send a notification on Linux, trying available notifiers.
     *
     * @return bool
     */
    protected function sendLinux() {
        $finder = new \Symfony\Component\Process\ExecutableFinder();

        if ($finder->find('notify-send') !== null) {
            return $this->sendLinuxNotifySend();
        }

        if ($finder->find('kdialog') !== null) {
            return $this->sendLinuxKDialog();
        }

        return false;
    }

    /**
     * Send a notification using notify-send.
     *
     * @return bool
     */
    protected function sendLinuxNotifySend() {
        $command = ['notify-send'];

        if ($this->icon !== '') {
            $command[] = '--icon';
            $command[] = $this->icon;
        }

        $command[] = $this->title;

        if ($this->body !== '') {
            $command[] = $this->body;
        }

        return $this->execute($command);
    }

    /**
     * Send a notification using kdialog.
     *
     * @return bool
     */
    protected function sendLinuxKDialog() {
        $message = $this->body !== '' ? "{$this->title}: {$this->body}" : $this->title;

        return $this->execute(['kdialog', '--passivepopup', $message, '5', '--title', $this->title]);
    }

    /**
     * Execute a command and return whether it was successful.
     *
     * @param array<int, string> $command
     *
     * @return bool
     */
    protected function execute(array $command) {
        $process = new \Symfony\Component\Process\Process($command);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Escape a string for use in AppleScript.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeAppleScript($value) {
        return '"' . str_replace(['\\', '"'], ['\\\\', '\\"'], $value) . '"';
    }

    /**
     * Send the notification.
     *
     * @return void
     */
    public function display() {
        $this->prompt();
    }

    /**
     * Get the value of the prompt.
     *
     * @return bool
     */
    public function value() {
        return true;
    }
}

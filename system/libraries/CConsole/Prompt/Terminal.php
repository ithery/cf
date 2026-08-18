<?php

class CConsole_Prompt_Terminal {
    /**
     * The initial TTY mode.
     *
     * @var null|string
     */
    protected $initialTtyMode;

    /**
     * Whether the terminal supports true color.
     *
     * @var null|bool
     */
    protected static $trueColorSupport;

    /**
     * The terminal's foreground color as an RGB array.
     *
     * @var null|array{int, int, int}
     */
    protected static $foregroundColor;

    /**
     * The terminal's background color as an RGB array.
     *
     * @var null|array{int, int, int}
     */
    protected static $backgroundColor;

    /**
     * The Symfony Terminal instance.
     *
     * @var \Symfony\Component\Console\Terminal
     */
    protected $terminal;

    /**
     * Create a new Terminal instance.
     */
    public function __construct() {
        $this->terminal = new \Symfony\Component\Console\Terminal();
    }

    /**
     * Read a line from the terminal.
     *
     * @return string
     */
    public function read() {
        $input = fread(STDIN, 1024);

        return $input !== false ? $input : '';
    }

    /**
     * Set the TTY mode.
     *
     * @param string $mode
     *
     * @return void
     */
    public function setTty($mode) {
        if (!isset($this->initialTtyMode)) {
            $this->initialTtyMode = $this->exec('stty -g');
        }

        $this->exec("stty {$mode}");
    }

    /**
     * Restore the initial TTY mode.
     *
     * @return void
     */
    public function restoreTty() {
        if (isset($this->initialTtyMode)) {
            $this->exec("stty {$this->initialTtyMode}");

            $this->initialTtyMode = null;
        }
    }

    /**
     * Get the number of columns in the terminal.
     *
     * @return int
     */
    public function cols() {
        return $this->terminal->getWidth();
    }

    /**
     * Get the number of lines in the terminal.
     *
     * @return int
     */
    public function lines() {
        return $this->terminal->getHeight();
    }

    /**
     * (Re)initialize the terminal dimensions.
     *
     * @return void
     */
    public function initDimensions() {
        (new ReflectionClass($this->terminal))
            ->getMethod('initDimensions')
            ->invoke($this->terminal);
    }

    /**
     * Exit the interactive session.
     *
     * @return void
     */
    public function exit() {
        exit(1);
    }

    /**
     * Execute the given command and return the output.
     *
     * @param string $command
     *
     * @return string
     */
    protected function exec($command) {
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (!$process) {
            throw new RuntimeException('Failed to create process.');
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        $code = proc_close($process);

        if ($code !== 0 || $stdout === false) {
            throw new RuntimeException(trim($stderr ?: "Unknown error (code: {$code})"), $code);
        }

        return $stdout;
    }

    /**
     * Determine if the terminal supports true color (24-bit).
     *
     * @return bool
     */
    public function supportsTrueColor() {
        if (!isset(static::$trueColorSupport)) {
            static::$trueColorSupport = in_array(getenv('COLORTERM'), ['truecolor', '24bit']);
        }

        return static::$trueColorSupport;
    }

    /**
     * Get the terminal's foreground color as an RGB array.
     *
     * @return array{int, int, int}
     */
    public function foregroundColor() {
        if (static::$foregroundColor === null) {
            $this->queryColors();
        }

        return static::$foregroundColor;
    }

    /**
     * Get the terminal's background color as an RGB array.
     *
     * @return array{int, int, int}
     */
    public function backgroundColor() {
        if (static::$backgroundColor === null) {
            $this->queryColors();
        }

        return static::$backgroundColor;
    }

    /**
     * Query the terminal for foreground and background colors in a single shot.
     *
     * @return void
     */
    protected function queryColors() {
        $savedStty = trim((string) shell_exec('stty -g < /dev/tty'));

        shell_exec('stty raw -echo min 0 time 1 < /dev/tty');

        fwrite(STDOUT, "\e]10;?\e\\\e]11;?\e\\");
        fflush(STDOUT);

        $ttyIn = fopen('/dev/tty', 'r');

        if ($ttyIn === false) {
            static::$foregroundColor = [204, 204, 204];
            static::$backgroundColor = [0, 0, 0];

            return;
        }

        $response = fread($ttyIn, 200);
        fclose($ttyIn);

        shell_exec("stty {$savedStty} < /dev/tty");

        preg_match_all('/rgb:([0-9a-f]+)\/([0-9a-f]+)\/([0-9a-f]+)/i', $response ?: '', $matches, PREG_SET_ORDER);

        $parse = function (array $m) {
            return [
                (int) (hexdec($m[1]) / (strlen($m[1]) === 4 ? 257 : 1)),
                (int) (hexdec($m[2]) / (strlen($m[2]) === 4 ? 257 : 1)),
                (int) (hexdec($m[3]) / (strlen($m[3]) === 4 ? 257 : 1)),
            ];
        };

        static::$foregroundColor = isset($matches[0]) ? $parse($matches[0]) : [204, 204, 204];
        static::$backgroundColor = isset($matches[1]) ? $parse($matches[1]) : [0, 0, 0];
    }
}

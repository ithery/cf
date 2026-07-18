<?php

trait CConsole_Prompt_Concerns_FakesInputOutput {
    /**
     * Fake the terminal and queue key presses to be simulated.
     *
     * @param array<string> $keys
     *
     * @return void
     */
    public static function fake(array $keys = []) {
        // Force interactive mode when testing because we will be mocking the terminal.
        static::interactive();

        $mock = Mockery::mock(CConsole_Prompt_Terminal::class);

        $mock->shouldReceive('write')->byDefault();
        $mock->shouldReceive('exit')->byDefault();
        $mock->shouldReceive('setTty')->byDefault();
        $mock->shouldReceive('restoreTty')->byDefault();
        $mock->shouldReceive('cols')->byDefault()->andReturn(80);
        $mock->shouldReceive('lines')->byDefault()->andReturn(24);
        $mock->shouldReceive('initDimensions')->byDefault();
        $mock->shouldReceive('supportsTrueColor')->byDefault()->andReturn(false);

        static::fakeKeyPresses($keys, function ($key) use ($mock) {
            $mock->shouldReceive('read')->once()->andReturn($key);
        });

        static::$terminal = $mock;

        self::setOutput(new CConsole_Prompt_Output_BufferedConsoleOutput());
    }

    /**
     * Implementation of the looping mechanism for simulating key presses.
     *
     * By ignoring the `$callable` parameter which contains the default logic
     * for simulating fake key presses, we can use a custom implementation
     * to emit key presses instead, allowing us to use different inputs.
     *
     * @param array<string> $keys
     * @param callable      $callable callable(string $key): void
     *
     * @return void
     */
    public static function fakeKeyPresses(array $keys, callable $callable) {
        foreach ($keys as $key) {
            $callable($key);
        }
    }

    /**
     * Assert that the output contains the given string.
     *
     * @param string $string
     *
     * @return void
     */
    public static function assertOutputContains($string) {
        PHPUnit\Framework\Assert::assertStringContainsString($string, static::content());
    }

    /**
     * Assert that the output doesn't contain the given string.
     *
     * @param string $string
     *
     * @return void
     */
    public static function assertOutputDoesntContain($string) {
        PHPUnit\Framework\Assert::assertStringNotContainsString($string, static::content());
    }

    /**
     * Assert that the stripped output contains the given string.
     *
     * @param string $string
     *
     * @return void
     */
    public static function assertStrippedOutputContains($string) {
        PHPUnit\Framework\Assert::assertStringContainsString($string, static::strippedContent());
    }

    /**
     * Assert that the stripped output doesn't contain the given string.
     *
     * @param string $string
     *
     * @return void
     */
    public static function assertStrippedOutputDoesntContain($string) {
        PHPUnit\Framework\Assert::assertStringNotContainsString($string, static::strippedContent());
    }

    /**
     * Get the buffered console output.
     *
     * @return string
     */
    public static function content() {
        if (!static::output() instanceof CConsole_Prompt_Output_BufferedConsoleOutput) {
            throw new RuntimeException('Prompt must be faked before accessing content.');
        }

        return static::output()->content();
    }

    /**
     * Get the buffered console output, stripped of escape sequences.
     *
     * @return string
     */
    public static function strippedContent() {
        return preg_replace("/\e\[[0-9;?]*[A-Za-z]/", '', static::content());
    }
}

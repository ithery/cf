<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class CommandTest extends TestCase {
    protected function tearDown() {
        m::close();
    }

    public function testGettingCommandArgumentsAndOptionsByClass() {
        $command = new class() extends CConsole_Command {
            public function handle() {
            }

            protected function getArguments() {
                return [
                    new InputArgument('argument-one', InputArgument::REQUIRED, 'first test argument'),
                    ['argument-two', InputArgument::OPTIONAL, 'a second test argument'],
                ];
            }

            protected function getOptions() {
                return [
                    new InputOption('option-one', 'o', InputOption::VALUE_OPTIONAL, 'first test option'),
                    ['option-two', 't', InputOption::VALUE_REQUIRED, 'second test option'],
                ];
            }
        };

        $input = new ArrayInput([
            'argument-one' => 'test-first-argument',
            'argument-two' => 'test-second-argument',
            '--option-one' => 'test-first-option',
            '--option-two' => 'test-second-option',
        ]);
        $output = new NullOutput();

        $command->run($input, $output);

        $this->assertSame('test-first-argument', $command->argument('argument-one'));
        $this->assertSame('test-second-argument', $command->argument('argument-two'));
        $this->assertSame('test-first-option', $command->option('option-one'));
        $this->assertSame('test-second-option', $command->option('option-two'));
    }

    public function testTheInputSetterOverwrite() {
        $input = m::mock(InputInterface::class);
        $input->shouldReceive('hasArgument')->once()->with('foo')->andReturn(false);

        $command = new CConsole_Command();
        $command->setInput($input);

        $this->assertFalse($command->hasArgument('foo'));
    }

    public function testTheOutputSetterOverwrite() {
        $output = m::mock(CConsole_OutputStyle::class);
        $output->shouldReceive('writeln')->once()->withArgs(function (...$args) {
            return $args[0] === '<info>foo</info>';
        });

        $command = new CConsole_Command();
        $command->setOutput($output);

        $command->info('foo');
    }

    public function testChoiceIsSingleSelectByDefault() {
        $output = m::mock(CConsole_OutputStyle::class);
        $output->shouldReceive('askQuestion')->once()->withArgs(function (ChoiceQuestion $question) {
            return $question->isMultiselect() === false;
        });

        $command = new CConsole_Command();
        $command->setOutput($output);

        $command->choice('Do you need further help?', ['yes', 'no']);
    }

    public function testChoiceWithMultiselect() {
        $output = m::mock(CConsole_OutputStyle::class);
        $output->shouldReceive('askQuestion')->once()->withArgs(function (ChoiceQuestion $question) {
            return $question->isMultiselect() === true;
        });

        $command = new CConsole_Command();
        $command->setOutput($output);

        $command->choice('Select all that apply.', ['option-1', 'option-2', 'option-3'], null, null, true);
    }
}

# CF Command - Basic

### Output

Several methods are available for writing output to the console:

```php
// Information (green)
$this->info('Process finished');

// Error (red)
$this->error('Something went wrong');

// Warning (yellow)
$this->warn('Take note');

// Plain text
$this->line('Plain text');

// Table
$this->table(['Header 1', 'Header 2'], [
    ['Row 1 Col 1', 'Row 1 Col 2'],
    ['Row 2 Col 1', 'Row 2 Col 2'],
]);
```

### Interactive input

```php
// Question
$name = $this->ask('What is your name?');

// Question with a default
$name = $this->ask('What is your name?', 'Default');

// Confirmation
if ($this->confirm('Are you sure?')) {
    // ...
}

// Choice
$option = $this->choice('Pick a colour', ['red', 'blue', 'green']);
```

### Accessing arguments and options

```php
public function handle() {
    // A single argument
    $user = $this->argument('user');

    // Every argument
    $arguments = $this->arguments();

    // A single option
    $queue = $this->option('queue');

    // Every option
    $options = $this->options();
}
```

### Application-aware commands

For commands that need the application context — database, configuration, and so on — extend
`CConsole_Command_AppCommand`:

```php
class CConsole_Command_MyAppCommand extends CConsole_Command_AppCommand {
    protected $signature = 'my:app-command';

    protected $description = 'A command that requires the application context';

    public function handle() {
        // Database, configuration, and the rest are already available
        $db = c::db();
    }
}
```

### Calling a command from code

```php
// From a controller or any other code
CConsole::call('my:command', ['argument' => 'value', '--option' => 'value']);
```

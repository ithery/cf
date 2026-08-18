# CF Command - Introduction

### Creating a command

CF lets an application define its own console commands. A command is created by extending the
`CConsole_Command` class.

### Command structure

A command class has two main properties and one method that must be implemented:

```php
class CConsole_Command_MyCommand extends CConsole_Command {
    protected $signature = 'my:command {argument} {--option=}';

    protected $description = 'Command description';

    public function handle() {
        $argument = $this->argument('argument');
        $option = $this->option('option');

        $this->info('Command completed');
    }
}
```

### Signature

The `$signature` property defines the command name together with the arguments and options it
accepts:

```php
// Required argument
protected $signature = 'mail:send {user}';

// Optional argument
protected $signature = 'mail:send {user?}';

// Argument with a default value
protected $signature = 'mail:send {user=foo}';

// Option that takes a value
protected $signature = 'mail:send {--queue=}';

// Boolean option (flag)
protected $signature = 'mail:send {--queue}';

// Variadic argument
protected $signature = 'mail:send {user*}';
```

### Registering a command

Every default command is defined in `CFConsole` at `system/core/CFConsole.php`, in the
`$defaultCommands` property.

To register a custom command from an application, use the `addCommand` method:

```php
CFConsole::addCommand(MyCustomCommand::class);
```

Or several at once:

```php
CFConsole::addCommand([
    MyCommand1::class,
    MyCommand2::class,
]);
```

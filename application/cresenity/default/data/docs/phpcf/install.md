# PHPCF - Installation

### Install With Composer

PHPCF is the command line tool for managing Cresenity Framework applications. Install it
globally through Composer:

```
composer global require cresenity/phpcf
```

Make sure the global composer `vendor/bin` directory is on your system `PATH`.

### Usage

Once installed, run `phpcf` from the project root directory:

```
phpcf list
```

### Multiple applications

Because CF serves multiple applications, commands run in the context of a single
domain/application. Check the active domain with:

```
phpcf status
```

Use `domain:switch` to change application:

```
phpcf domain:switch domain.name
```

### Command list

Every default command is defined in the `CFConsole` class at `system/core/CFConsole.php`. See
the menu alongside for the commands grouped by category.

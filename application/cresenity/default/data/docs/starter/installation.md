# Installation

## Requirements

Before installing Cresenity Framework, make sure your environment meets the following requirements:

- PHP >= 7.4
- Composer (globally installed)
- Git
- Apache with `mod_rewrite` enabled (or Nginx equivalent)

For a full list of required PHP extensions, see [Configuration](/docs/starter/configuration).

---

## 1. Install the `phpcf` CLI Tool

The `phpcf` command-line tool is used to create and manage Cresenity applications.

```bash
composer global require cresenity/phpcf
```

Make sure the Composer global `vendor/bin` directory is in your system `PATH`. You can verify the installation by running:

```bash
phpcf --version
```

---

## 2. Clone the Framework

```bash
git clone git@github.com:cresenity/cf.git
cd cf
```

After cloning, copy the sample entry point to create your `index.php`:

```bash
cp index.php.sample index.php
```

---

## 3. Set Up an Existing Project

If you are working on an existing project, clone its repository into the `application/` directory:

```bash
cd application
git clone git@github.com:your-org/myproject.git
```

This will create the structure `application/myproject/`.

---

## 4. Create a New Project

To scaffold a new application, use the `phpcf` CLI tool from the framework root directory:

```bash
phpcf app:create myproject
```

You will be prompted to enter a unique application code.

### Configure the Domain

1. Create a domain configuration file in the `data/domain/` directory. If the `data/` directory does not exist, create it first:

    ```bash
    mkdir -p data/domain
    ```

2. Create a file named after your development domain, for example `myproject.dev.cresenity.com.php`:

    ```php
    <?php
    return [
        'app_code' => 'myproject',
        'app_id'   => '1',
        'org_code' => 'myproject',
        'org_id'   => 1,
    ];
    ```

3. Point your local web server (or `/etc/hosts`) to the domain and open it in your browser:

    ```
    http://myproject.dev.cresenity.com
    ```

---

## Next Steps

- [Directory Structure](/docs/starter/directory) — understand how the framework is organized
- [Configuration](/docs/starter/configuration) — configure your application
- [Routing](/docs/basic/routing) — learn how URLs map to controllers

#!/usr/bin/env node

// @ts-check
const { Command } = require('commander');
const { spawn } = require('child_process');
const path = require('path');
const pkg = require('../../package.json');
const { assertSupportedNodeVersion } = require('./src/Engine.js');

run().catch(err => {
    console.error(err);

    process.exitCode = process.exitCode || 1;
    process.exit();
});

/**
 * Run the program.
 */
 async function run() {
    const program = new Command();

    program.name('cf');
    program.version(pkg.version);
    program.option(
        '--cf-config <path>',
        'The path to your CF configuration file.',
        'webpack.cf'
    );
    program.option('--no-progress', 'Disable progress reporting', false);

    program
        .command('watch')
        .description('Build and watch files for changes.')
        .option('--hot', 'Enable hot reloading.', false)
        .option('--https', 'Enable https.', false)
        .action((opts, cmd) =>
            executeScript('watch', { ...program.opts(), ...opts }, cmd.args)
        );

    program
        .command('build', { isDefault: true })
        .description('Compile CF.')
        .option('-p, --production', 'Run CF in production mode.', false)
        .action((opts, cmd) =>
            executeScript('build', { ...program.opts(), ...opts }, cmd.args)
        );

    await program.parseAsync(process.argv);
}


/**
 * Execute the script.
 *
 * @param {"build"|"watch"} cmd
 * @param {{[key: string]: any}} opts
 * @param {string[]} args
 */
 async function executeScript(cmd, opts, args = []) {
    assertSupportedNodeVersion();

    const env = getEffectiveEnv(opts);

    // We MUST use a relative path because the files
    // created by npm dont correctly handle paths
    // containg spaces on Windows (yarn does)
    const configPath = path.relative(
        process.cwd(),
        require.resolve('./setup/webpack.config.js')
    );

    const script = [
        commandScript(cmd, opts),
        `--config="${configPath}"`,
        ...quoteArgs(args)
    ].join(' ');
    console.log(script);
    const scriptEnv = {
        NODE_ENV: env,
        CF_FILE: opts.cfConfig
    };

    if (isTesting()) {
        process.stdout.write(
            JSON.stringify({
                script,
                env: scriptEnv
            })
        );

        return;
    }

    function restart() {
        let child = spawn(script, {
            stdio: 'inherit',
            shell: true,
            env: {
                ...process.env,
                ...scriptEnv
            }
        });

        let shouldOverwriteExitCode = true;

        child.on('exit', (code, signal) => {
            // Note adapted from cross-env:
            // https://github.com/kentcdodds/cross-env/blob/3edefc7b450fe273655664f902fd03d9712177fe/src/index.js#L30-L31

            // The process exit code can be null when killed by the OS (like an out of memory error) or sometimes by node
            // SIGINT means the _user_ pressed Ctrl-C to interrupt the process execution
            // Return the appropriate error code in that case
            if (code === null) {
                code = signal === 'SIGINT' ? 130 : 1;
            }

            if (shouldOverwriteExitCode) {
                process.exitCode = code;
            }
        });

        process.on('SIGINT', () => {
            shouldOverwriteExitCode = false;
            child.kill('SIGINT');
        });

        process.on('SIGTERM', () => {
            shouldOverwriteExitCode = false;
            child.kill('SIGTERM');
        });
    }

    restart();
}

/**
 * Get the command-specific portion of the script.
 *
 * @param {"build"|"watch"} cmd
 * @param {{[key: string]: any}} opts
 */
function commandScript(cmd, opts) {
    const showProgress = isTTY() && opts.progress;

    if (cmd === 'build') {
        if (showProgress) {
            return 'npx webpack --progress';
        }

        return 'npx webpack';
    } else if (cmd === 'watch' && !opts.hot) {
        if (showProgress) {
            return 'npx webpack --progress --watch';
        }

        return 'npx webpack --watch';
    } else if (cmd === 'watch' && opts.hot) {
        return 'npx webpack serve --hot' + (opts.https ? ' --https' : '');
    }
}

/**
 * Get the command arguments with quoted values.
 *
 * @param {string[]} args
 */
function quoteArgs(args) {
    return args.map(arg => {
        // Split string at first = only
        const pattern = /^([^=]+)=(.*)$/;
        const keyValue = arg.includes('=') ? pattern.exec(arg).slice(1) : [];

        if (keyValue.length === 2) {
            return `${keyValue[0]}="${keyValue[1]}"`;
        }

        return arg;
    });
}

/**
 * Get the effective envirnoment to run in
 *
 ** @param {{[key: string]: any}} opts
 */
function getEffectiveEnv(opts) {
    // If we've requested a production compile we enforce use of the production env
    // If we don't a user's global NODE_ENV may override and prevent minification of assets
    if (opts.production) {
        return 'production';
    }

    // We use `development` by default or under certain specific conditions when testing
    if (!process.env.NODE_ENV || (isTesting() && process.env.NODE_ENV === 'test')) {
        return 'development';
    }

    // Otherwsise defer to the current value of NODE_ENV
    return process.env.NODE_ENV;
}

function isTesting() {
    return process.env.TESTING;
}

function isTTY() {
    if (isTesting() && process.env.IS_TTY !== undefined) {
        return process.env.IS_TTY === 'true';
    }

    if (isTesting() && process.stdout.isTTY === undefined) {
        return true;
    }

    return process.stdout.isTTY;
}
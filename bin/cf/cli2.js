const exec = require('child_process').exec;

const path = require('path');
const pkg = require('../../package.json');

const argv = process.argv;
if(argv.length<=2) {
    console.log("CF NPM Version:" + pkg.version);
    process.exit();
}

let appCode = argv[2];

let appPath = path.resolve('./application/'+appCode);

if(argv.length>3) {
    let parameters = argv.slice(3);

    let command = [
        'npm',
        ...parameters
    ].join(' ');
    console.log("Directory:"+appPath);
    console.log("Command:"+command);
    let appProcess = exec(command, {cwd:appPath});
    appProcess.stdout.on('data', function (data) {
        process.stdout.write(data);
    });

    appProcess.stderr.on('data', function (data) {
        process.stderr.write(data);
    });

    appProcess.on('exit', function (code) {
        //console.log('child process exited with code ' + code.toString());
    });
} else {
    exec('npm run build',{cwd:appPath});
}

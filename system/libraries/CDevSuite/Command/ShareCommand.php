<?php

/**
 * Description of ShareCommand
 *
 * @author Hery
 */
class CDevSuite_Command_ShareCommand extends CDevSuite_CommandAbstract {

    public function getSignatureArguments() {
        return '{name}';
    }

    public function run(CConsole_Command $cfCommand) {
        $host = $cfCommand->argument('name');
        $tld = CDevSuite::tld();
        $port = CDevSuite::site()->port("$host.$tld");
        $port = $port === 443 ? 60 : $port;

        switch (CServer::getOS()) {
            case CServer::OS_LINUX:
            case CServer::OS_DARWIN:
                $ngrok = realpath(CDevSuite::binPath() . 'ngrok');
                $ngrokCommand = "\"$ngrok\" http $host.$tld:$port -host-header=rewrite";



                $startNgrokCommand = sprintf('sudo %s', $ngrokCommand);

                CDevSuite::info('Executing Command:' . $startNgrokCommand);

                CDevSuite::commandLine()->run($startNgrokCommand);

                break;
            case CServer::OS_WINNT:

                $ngrok = realpath(CDevSuite::binPath() . 'ngrok.exe');

                $ngrokCommand = "\"$ngrok\" http $host.$tld:$port -host-header=rewrite";
                passthru("start \"$host.$tld\" " . $ngrokCommand);
                break;
        }
    }

}

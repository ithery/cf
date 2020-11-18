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
        $ngrok = realpath(CDevSuite::binPath() . 'ngrok.exe');

        passthru("start \"$host.$tld\" \"$ngrok\" http $host.$tld:$port -host-header=rewrite");
    }

}

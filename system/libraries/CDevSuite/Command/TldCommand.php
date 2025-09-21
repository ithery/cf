<?php

/**
 * Description of TldCommand.
 *
 * @author Hery
 */
class CDevSuite_Command_TldCommand extends CDevSuite_CommandAbstract {
    public function getSignatureArguments() {
        return '{tld}';
    }

    public function run(CConsole_Command $cfCommand) {
        $tld = $cfCommand->argument('tld');
        if ($tld === null) {
            return CDevSuite::info(CDevSuite::configuration()->read()['tld']);
        }

        $oldTld = CDevSuite::configuration()->read()['tld'];

        if (CServer::getOS() == CServer::OS_WINNT) {
            CDevSuite::acrylic()->updateTld($tld);
        } else {
            CDevSuite::acrylic()->updateTld(
                $oldTld,
                $tld = trim($tld, '.')
            );
        }

        CDevSuite::configuration()->updateKey('tld', $tld);

        CDevSuite::site()->resecureForNewTld($oldTld, $tld);
        CDevSuite::phpFpm()->restart();
        CDevSuite::nginx()->restart();
        CDevSuite::info('Your DevSuite TLD has been updated to [' . $tld . '].');
    }
}

<?php

/**
 * Description of PruneBootstrapper
 *
 * @author Hery
 */
class CDevSuite_Bootstrap_PruneBootstrapper extends CDevSuite_Bootstrap_Bootstrapper {
    public function bootstrap() {
        /*
         * Prune missing directories and symbolic links on every command.
         */
        if (is_dir(CDevSuite::homePath())) {
            /*
             * Upgrade helper: ensure the tld config exists
             */
            if (empty(CDevSuite::configuration()->read()['tld'])) {
                CDevSuite::configuration()->writeBaseConfiguration();
            }

            CDevSuite::configuration()->prune();

            CDevSuite::site()->pruneLinks();
        }
    }
}

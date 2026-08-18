<?php

use CModel_Console_PropertiesHelper as Helper;

class CConsole_Command_Asset_GoogleFontsFetchCommand extends CConsole_Command_AppCommand {
    public $signature = 'asset:google-fonts:fetch';

    public $description = 'Fetch Google Fonts and store them on a local disk';

    public function handle() {
        $this->info('Start fetching Google Fonts...');

        c::collect(CF::config('assets.google_fonts.fonts'))
            ->keys()
            ->each(function (string $font) {
                $this->info("Fetching `{$font}`...");

                c::manager()->googleFonts()->load(compact('font'), $forceDownload = true);
            });

        $this->info('All done!');
    }
}

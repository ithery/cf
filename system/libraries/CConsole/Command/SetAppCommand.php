<?php
class CConsole_Command_SetAppCommand extends CConsole_Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set:app {appCode}';

    public function handle() {
        $appCode = $this->argument('appCode');
        if (CConsole::appCode() == $appCode) {
            $this->info('You are already on appCode:' . $appCode);
        } else {
            if (!CF::appCodeExists($appCode)) {
                $this->error('Failed set appCode, ' . $appCode . ' not exists');
            } else {
                $fileData = CF::CFCLI_CURRENT_APPCODE_FILE;
                CFile::put($fileData, $appCode, true);
                $this->info('Succesfully set app to ' . $appCode);
            }
        }
    }
}

<?php

class CConsole_Command_Translation_Check extends CConsole_Command_AppCommand {
    /**
     * @var array
     */
    public $realLines = [];

    /**
     * @var array
     */
    public $excludedDirectories = [];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if all translations are there for all languages.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $directory = c::untrailingslashit(c::appRoot('default/i18n'));

        if (!$this->checkIfDirectoryExists($directory)) {
            $this->error('The passed directory (' . $directory . ') does not exist.');

            return $this::FAILURE;
        }

        $languages = $this->getLanguages($directory);
        $missingFiles = [];

        $path = $directory;
        $rdi = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::KEY_AS_PATHNAME);
        foreach (new RecursiveIteratorIterator($rdi, RecursiveIteratorIterator::SELF_FIRST) as $langFile => $info) {
            if (!CFile::isDirectory($langFile) && cstr::endsWith($langFile, ['.json', '.php'])) {
                $fileName = basename($langFile);
                $languageDir = cstr::replace($fileName, '', $langFile);
                $languagesWithMissingFile = $this->checkIfFileExistsForOtherLanguages($languages, $fileName, $directory);

                if ($this->isDirInExcludedDirectories($languageDir)) {
                    continue;
                }

                foreach ($languagesWithMissingFile as $languageWithMissingFile) {
                    if ($this->isDirInExcludedDirectories($languageWithMissingFile)) {
                        continue;
                    }

                    $missingFiles[] = 'The language ' . $languageWithMissingFile . ' (' . $directory . '/' . $languageWithMissingFile . ') is missing the file ( ' . $fileName . ' )';
                }
                $this->handleFile($languageDir, $langFile);
            }
        }
        $missing = [];
        foreach ($this->realLines as $key => $line) {
            foreach ($languages as $language) {
                $fileKey = basename($key);

                $exists = array_key_exists($directory . DIRECTORY_SEPARATOR . $language . DIRECTORY_SEPARATOR . $fileKey, $this->realLines);

                if ($this->isDirInExcludedDirectories($language)) {
                    continue;
                }
                if (!$exists) {
                    $fileName = cstr::replace(['.php', '.json'], '', $fileKey);

                    $missing[] = $language . '.' . $fileName;
                }
            }
        }

        foreach ($missingFiles as $missingFile) {
            $this->error($missingFile);
        }

        foreach ($missing as $missingTranslation) {
            $this->error('Missing the translation with key: ' . $missingTranslation);
        }

        if (count($missingFiles) === 0 && count($missing) === 0) {
            $this->info('✔ All translations are okay!');
        }

        return count($missing) > 0 || count($missingFiles) > 0 ? $this::FAILURE : $this::SUCCESS;
    }

    public function handleFile($languageDir, $langFile): void {
        $fileName = basename($langFile);

        if (cstr::endsWith($fileName, '.json')) {
            $lines = json_decode(CFile::get($langFile), true);
        } else {
            $lines = include $langFile;
        }

        if (!is_array($lines)) {
            $this->warn('Skipping file (' . $langFile . ') because it is empty.');

            return;
        }

        foreach ($lines as $index => $line) {
            if (is_array($line)) {
                foreach ($line as $index2 => $line2) {
                    $this->realLines[$languageDir . $fileName . '.' . $index . '.' . $index2] = $line2;
                }
            } else {
                $this->realLines[$languageDir . $fileName . '.' . $index] = $line;
            }
        }
    }

    /**
     * @param string $directory
     *
     * @return bool
     */
    private function checkIfDirectoryExists(string $directory): bool {
        return CFile::isDirectory($directory);
    }

    /**
     * @param string $directory
     *
     * @return array
     */
    private function getLanguages(string $directory): array {
        $languages = [];

        if ($handle = opendir($directory)) {
            while (false !== ($languageDir = readdir($handle))) {
                if ($languageDir !== '.' && $languageDir !== '..') {
                    $languages[] = $languageDir;
                }
            }
        }

        closedir($handle);

        return $languages;
    }

    /**
     * @param $languages
     * @param $fileName
     * @param $baseDirectory
     *
     * @return array
     */
    private function checkIfFileExistsForOtherLanguages($languages, $fileName, $baseDirectory): array {
        $languagesWhereFileIsMissing = [];

        foreach ($languages as $language) {
            if (!CFile::exists($baseDirectory . '/' . $language . '/' . $fileName)) {
                $languagesWhereFileIsMissing[] = $language;
            }
        }

        return $languagesWhereFileIsMissing;
    }

    private function isDirInExcludedDirectories($directoryToCheck): bool {
        foreach ($this->excludedDirectories as $excludedDirectory) {
            if (cstr::contains($directoryToCheck, $excludedDirectory)) {
                return true;
            }
        }

        return false;
    }
}

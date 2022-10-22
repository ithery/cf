<?php

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class CQC_Testing_Repository {
    use CQC_Testing_Repository_Concern_SuiteTrait;
    use CQC_Testing_Repository_Concern_TestTrait;

    protected $ansiConverter;

    /**
     * @var \CCollection
     */
    protected $messages;

    public function __construct() {
        $this->ansiConverter = new AnsiToHtmlConverter();
        $this->messages = c::collect();
    }

    /**
     * Carriage return to <br>.
     *
     * @param $lines
     *
     * @return string
     */
    protected function crToBr($lines) {
        return str_replace("\n", '<br>', $lines);
    }

    /**
     * <br> to carriage return.
     *
     * @param $lines
     *
     * @return string
     */
    protected function brToCr($lines) {
        return str_replace('<br>', "\n", $lines);
    }

    /**
     * Create link to call the editor for a file.
     *
     * @param $test
     * @param $fileName
     * @param $line
     * @param $occurrence
     *
     * @return string
     */
    protected function createLinkToEditFile($test, $fileName, $line, $occurrence) {
        if (!$this->fileExistsOnTest($fileName, $test)) {
            return $line[$occurrence];
        }

        $fileName = base64_encode($fileName);

        $tag = sprintf(
            '<a href="javascript:jQuery.get(\'%s\');" class="file">%s</a>',
            route(
                'tests-watcher.file.edit',
                [
                    'filename' => $fileName,
                    'suite_id' => $test->suite->suite_id,
                    'line' => $line[2],
                ]
            ),
            $line[$occurrence]
        );

        return $tag;
    }

    /**
     * Create links.
     *
     * @param $lines
     * @param $matches
     * @param mixed $test
     *
     * @return mixed
     */
    protected function createLinks($lines, $matches, $test) {
        foreach ($matches as $line) {
            if (!empty($line) && is_array($line) && count($line) > 0 && is_array($line[0]) && count($line[0]) > 0) {
                $occurence = strpos($lines, $line[0]) === false ? 1 : 0;

                $lines = str_replace(
                    $line[$occurence],
                    $this->createLinkToEditFile($test, $line[1], $line, $occurence),
                    $lines
                );
            }
        }

        return $lines;
    }

    /**
     * Find source code references.
     *
     * Must find
     *
     *   at Object..test (resources/assets/js/tests/example.spec.js:4:23
     *
     *   (resources/assets/js/tests/example.spec.js:4
     *
     *   /resources/assets/js/tests/example.php:449
     *
     * @param $lines
     * @param $test
     *
     * @return mixed
     */
    protected function findSourceCodeReferences($lines, $test) {
        preg_match_all(
            $this->config()->getRegexFileMatcher(),
            strip_tags($this->brToCr($lines)),
            $matches,
            PREG_SET_ORDER
        );

        return array_filter($matches);
    }

    /**
     * @return mixed
     */
    public function getAnsiConverter() {
        return $this->ansiConverter;
    }

    /**
     * Get a list of png files to store in database.
     *
     * @param $test
     * @param $log
     *
     * @return null|string
     */
    protected function getScreenshots($test, $log) {
        $screenshots = $test->suite->tester->name !== 'dusk'
            ? $this->getOutput($test, $test->suite->tester->output_folder, $test->suite->tester->output_png_fail_extension)
            : $this->parseDuskScreenshots($log, $test->suite->tester->output_folder);

        if (is_null($screenshots)) {
            return;
        }

        $screenshots = c::collect($screenshots)->map(function ($path) use ($test) {
            return replace_suite_paths($test->suite, $path);
        });

        return json_encode($screenshots->toArray());
    }

    /**
     * Check if the class is abstract.
     *
     * @param $file
     *
     * @return bool
     */
    protected function isAbstractClass($file) {
        return (bool) preg_match(
            '/^abstract\s+class[A-Za-z0-9_\s]{1,100}{/im',
            file_get_contents($file)
        );
    }

    /**
     * Check if the file is testable.
     *
     * @param $file
     *
     * @return bool
     */
    protected function isTestable($file) {
        return cstr::endsWith($file, '.php')
            ? !$this->isAbstractClass($file)
            : true;
    }

    /**
     * Create links for files.
     *
     * @param $lines
     * @param mixed $test
     *
     * @return string
     */
    protected function linkFiles($lines, $test) {
        $matches = $this->findSourceCodeReferences($lines, $test);

        if (count($matches) != 0) {
            $lines = $this->createLinks($lines, $matches, $test);
        }

        return $this->CRToBr($lines);
    }

    /**
     * Generate a lista of screenshots.
     *
     * @param $log
     * @param $folder
     *
     * @return null|array
     */
    protected function parseDuskScreenshots($log, $folder) {
        preg_match_all('/[0-9]\)+\s(.+::.*)/', $log, $matches, PREG_SET_ORDER);

        $result = [];

        foreach ($matches as $line) {
            $name = str_replace("\r", '', $line[1]);
            $name = str_replace('\\', '_', $name);
            $name = str_replace('::', '_', $name);

            $result[] = $folder . DIRECTORY_SEPARATOR . "failure-{$name}-0.png";
        }

        return count($result) == 0 ? null : $result;
    }

    /**
     * Remove before word.
     *
     * @param $diff
     *
     * @return mixed
     */
    protected function removeBefore($diff) {
        return str_replace('before', '', $diff);
    }

    /**
     * Properly render HTML source code.
     *
     * @param string $contents
     *
     * @return string
     */
    protected function renderHtml($contents) {
        return nl2br(
            htmlentities($contents)
        );
    }

    /**
     * Check if a path is excluded.
     *
     * @param $exclusions
     * @param $path
     * @param string $file
     *
     * @return bool
     */
    public function isExcluded($exclusions, $path, $file = '') {
        if ($file) {
            if (!$file instanceof SplFileInfo) {
                $path = c::makePath([$path, $file]);
            } else {
                $path = $file->getPathname();
            }
        }

        foreach ($exclusions ?: [] as $excluded) {
            if (cstr::startsWith($path, $excluded)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Format output log.
     *
     * @param $log
     * @param mixed $test
     *
     * @return mixed|string
     */
    public function formatLog($log, $test) {
        return !empty($log)
            ? $this->linkFiles($this->ansi2Html($log), $test)
            : $log;
    }

    /**
     * Convert output ansi chars to html.
     *
     * @param $log
     *
     * @return mixed|string
     */
    protected function ansi2Html($log) {
        $string = html_entity_decode(
            $this->ansiConverter->convert($log)
        );

        $string = str_replace("\r\n", '<br>', $string);

        $string = str_replace("\n", '<br>', $string);

        return $string;
    }

    /**
     * Remove ansi codes from string.
     *
     * @param $string
     *
     * @return string
     */
    public function removeAnsiCodes($string) {
        return strip_tags(
            $this->ansi2Html($string)
        );
    }

    /**
     * Get the test output.
     *
     * @param $test
     * @param $outputFolder
     * @param $extension
     *
     * @return null|string
     */
    protected function getOutput($test, $outputFolder, $extension) {
        if (empty($outputFolder)) {
            return;
        }

        $file = replace_suite_paths($test->suite, c::makePath([
            c::makePath([$test->suite->path, $outputFolder]),
            str_replace(['.php', '::', '\\', '/'], ['', '.', '', ''], $test->name) . $extension,
        ]));

        return file_exists($file) ? $this->renderHtml(file_get_contents($file)) : null;
    }

    /**
     * Encode a image or html for database storage.
     *
     * @param $file
     *
     * @return bool|mixed|string
     */
    protected function encodeFile($file) {
        $type = pathinfo($file, PATHINFO_EXTENSION);

        $data = file_get_contents($file);

        if ($type == 'html') {
            return $data;
        }

        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    /**
     * Check if a file exists for a particular test.
     *
     * @param $filename
     * @param $test
     *
     * @return bool
     */
    public function fileExistsOnTest($filename, $test) {
        return file_exists(
            $this->addProjectRootPath($filename, $test->suite)
        );
    }

    /**
     * Add project root to path.
     *
     * @param $fileName
     * @param $suite
     *
     * @return string
     */
    public function addProjectRootPath($fileName, $suite) {
        if (cstr::startsWith($fileName, DIRECTORY_SEPARATOR) || empty($suite)) {
            return $fileName;
        }

        return $suite->project->path . DIRECTORY_SEPARATOR . $fileName;
    }

    /**
     * Ansi converter setter.
     *
     * @param mixed $ansiConverter
     */
    public function setAnsiConverter($ansiConverter) {
        $this->ansiConverter = $ansiConverter;
    }

    /**
     * Normalize a path removing inconsistences.
     *
     * @param $path
     *
     * @return bool|mixed|string
     */
    public function normalizePath($path) {
        $path = trim($path);

        $path = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $path);

        if (cstr::endsWith($path, DIRECTORY_SEPARATOR)) {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    /**
     * Get all messages.
     *
     * @return Collection
     */
    public function getMessages() {
        return $this->messages;
    }

    /**
     * Add a message to the list.
     *
     * @param $type
     * @param $body
     *
     * @internal param $string
     * @internal param $string1
     */
    protected function addMessage($body, $type = 'line') {
        $this->messages->push(['type' => $type, 'body' => $body]);
    }

    /**
     * Set messages.
     *
     * @param \CCollection $messages
     */
    public function setMessages($messages) {
        $this->messages = $messages;
    }

    /**
     * @return CQC_Testing_Config
     */
    public function config() {
        return CQC_Testing_Config::instance();
    }

    /**
     * Get all tests.
     *
     * @return CCollection
     */
    public function getTests() {
        $order = "(case
						when state = 'running' then 1
						when state = 'failed' then 2
						when state = 'queued' then 3
						when state = 'ok' then 4
						when state = 'idle' then 5
			        end) asc,
			        updated desc";

        $query = CQC_Testing_Model_Test::orderByRaw($order);

        return c::collect($query->get())->map(function ($test) {
            return $this->getTestInfo($test);
        });
    }
}

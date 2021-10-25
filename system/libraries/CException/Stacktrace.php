<?php

class CException_Stacktrace {
    /**
     * @var \CException_Stacktrace_Frame[]
     */
    private $frames;

    /**
     * @var string
     */
    private $applicationPath;

    /**
     * @param Throwable   $throwable
     * @param null|string $applicationPath
     *
     * @return self
     */
    public static function createForThrowable($throwable, $applicationPath = null) {
        return new static($throwable->getTrace(),  $applicationPath, $throwable->getFile(), $throwable->getLine());
    }

    /**
     * @param null|string $applicationPath
     *
     * @return CException_Stacktrace
     */
    public static function create($applicationPath = null) {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS & ~DEBUG_BACKTRACE_PROVIDE_OBJECT);

        return new static($backtrace, $applicationPath);
    }

    /**
     * @param array       $backtrace
     * @param null|string $applicationPath
     * @param null|string $topmostFile
     * @param null|string $topmostLine
     */
    public function __construct($backtrace, $applicationPath = null, $topmostFile = null, $topmostLine = null) {
        if ($applicationPath == null) {
            $applicationPath = c::docRoot();
        }
        $this->applicationPath = $applicationPath;

        $currentFile = $topmostFile;
        $currentLine = $topmostLine;

        foreach ($backtrace as $rawFrame) {
            if (!$this->frameFromFlare($rawFrame) && !$this->fileIgnored($currentFile)) {
                $this->frames[] = new CException_Stacktrace_Frame(
                    $currentFile,
                    $currentLine,
                    $rawFrame['function'] ?? null,
                    $rawFrame['class'] ?? null,
                    $this->frameFileFromApplication($currentFile)
                );
            }

            $currentFile = $rawFrame['file'] ?? 'unknown';
            $currentLine = $rawFrame['line'] ?? 0;
        }

        $this->frames[] = new CException_Stacktrace_Frame(
            $currentFile,
            $currentLine,
            '[top]'
        );
    }

    /**
     * @param array $rawFrame
     *
     * @return bool
     */
    protected function frameFromFlare(array $rawFrame) {
        return isset($rawFrame['class']) && strpos($rawFrame['class'], 'Facade\\FlareClient\\') === 0;
    }

    /**
     * @param string $frameFilename
     *
     * @return bool
     */
    protected function frameFileFromApplication($frameFilename) {
        $relativeFile = str_replace('\\', DIRECTORY_SEPARATOR, $frameFilename);

        if (!empty($this->applicationPath)) {
            $relativeFile = array_reverse(explode($this->applicationPath ?? '', $frameFilename, 2))[0];
        }

        if (strpos($relativeFile, DIRECTORY_SEPARATOR . 'vendor') === 0) {
            return false;
        }

        return true;
    }

    /**
     * @param string $currentFile
     *
     * @return bool
     */
    protected function fileIgnored($currentFile) {
        $currentFile = str_replace('\\', DIRECTORY_SEPARATOR, $currentFile);

        $ignoredFiles = [
            '/ignition/src/helpers.php',
        ];

        foreach ($ignoredFiles as $ignoredFile) {
            if (strstr($currentFile, $ignoredFile) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return CException_Stacktrace_Frame
     */
    public function firstFrame() {
        return $this->frames[0];
    }

    /**
     * @return array
     */
    public function toArray() {
        return array_map(function (CException_Stacktrace_Frame $frame) {
            return $frame->toArray();
        }, $this->frames);
    }

    /**
     * Undocumented function.
     *
     * @return null|CException_Stacktrace_Frame
     */
    public function firstApplicationFrame() {
        foreach ($this->frames as $index => $frame) {
            if ($frame->isApplicationFrame()) {
                return $frame;
            }
        }

        return null;
    }

    /**
     * @return null|int
     */
    public function firstApplicationFrameIndex() {
        foreach ($this->frames as $index => $frame) {
            if ($frame->isApplicationFrame()) {
                return $index;
            }
        }

        return null;
    }
}

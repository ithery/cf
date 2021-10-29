<?php

class CException_Stacktrace_Frame {
    /**
     * @var string
     */
    private $file;

    /**
     * @var int
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $class;

    /**
     * @var bool
     */
    private $isApplicationFrame;

    /**
     * @param string      $file
     * @param int         $lineNumber
     * @param null|string $method
     * @param null|string $class
     * @param bool        $isApplicationFrame
     */
    public function __construct(
        $file,
        $lineNumber,
        $method = null,
        $class = null,
        $isApplicationFrame = false
    ) {
        $this->file = $file;

        $this->lineNumber = $lineNumber;

        $this->method = $method;

        $this->class = $class;

        $this->isApplicationFrame = $isApplicationFrame;
    }

    public function toArray() {
        $codeSnippet = (new CException_Stacktrace_CodeSnippet())
            ->snippetLineCount(31)
            ->surroundingLine($this->lineNumber)
            ->get($this->file);

        return [
            'line_number' => $this->lineNumber,
            'method' => $this->method,
            'class' => $this->class,
            'code_snippet' => $codeSnippet,
            'file' => $this->file,
            'is_application_frame' => $this->isApplicationFrame,
        ];
    }

    public function getFile() {
        return $this->file;
    }

    public function getLinenumber() {
        return $this->lineNumber;
    }

    public function isApplicationFrame() {
        return $this->isApplicationFrame;
    }
}

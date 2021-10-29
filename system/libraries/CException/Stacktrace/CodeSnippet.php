<?php

class CException_Stacktrace_CodeSnippet {
    /**
     * @var int
     */
    private $surroundingLine = 1;

    /**
     * @var int
     */
    private $snippetLineCount = 9;

    public function surroundingLine($surroundingLine) {
        $this->surroundingLine = $surroundingLine;

        return $this;
    }

    public function snippetLineCount($snippetLineCount) {
        $this->snippetLineCount = $snippetLineCount;

        return $this;
    }

    public function get($fileName) {
        if (!file_exists($fileName)) {
            return [];
        }

        try {
            $file = new CException_Stacktrace_File($fileName);

            list($startLineNumber, $endLineNumber) = $this->getBounds($file->numberOfLines());

            $code = [];

            $line = $file->getLine($startLineNumber);

            $currentLineNumber = $startLineNumber;

            while ($currentLineNumber <= $endLineNumber) {
                $code[$currentLineNumber] = rtrim(substr($line, 0, 250));

                $line = $file->getNextLine();
                $currentLineNumber++;
            }

            return $code;
        } catch (RuntimeException $exception) {
            return [];
        }
    }

    private function getBounds($totalNumberOfLineInFile) {
        $startLine = max($this->surroundingLine - floor($this->snippetLineCount / 2), 1);

        $endLine = $startLine + ($this->snippetLineCount - 1);

        if ($endLine > $totalNumberOfLineInFile) {
            $endLine = $totalNumberOfLineInFile;
            $startLine = max($endLine - ($this->snippetLineCount - 1), 1);
        }

        return [$startLine, $endLine];
    }
}

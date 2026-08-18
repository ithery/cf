<?php

trait CConsole_Trait_InteractsWithGitIgnoreTrait {
    protected $gitIgnoreContent;

    public function getGitIgnoreContent($force = false) {
        if ($force || $this->gitIgnoreContent == null) {
            $this->gitIgnoreContent = CFile::get($this->getGetIgnorePath());
        }

        return $this->gitIgnoreContent;
    }

    public function addGitIgnore(array $lines, $comment = '') {
        if (!$this->isAlreadyAddedOnGitIgnore($lines, $comment)) {
            $content = PHP_EOL;
            $content .= '## ' . $comment . PHP_EOL;
            foreach ($lines as $line) {
                $content .= $line . PHP_EOL;
            }
            $content .= PHP_EOL;
            CFile::append($this->getGetIgnorePath(), $content);
            $this->gitIgnoreContent = CFile::get($this->getGetIgnorePath());

            return $content;
        }

        return false;
    }

    protected function isAlreadyAddedOnGitIgnore(array $lines, $comment = '') {
        $isAdded = false;
        foreach ($lines as $line) {
            $isAdded = $isAdded || strpos($this->getGitIgnoreContent(), $line) !== false;
        }
        $isAdded = $isAdded || strpos($this->getGitIgnoreContent(), $comment) !== false;

        return $isAdded;
    }

    public function getGetIgnorePath() {
        if (CF::appCode()) {
            return rtrim(CF::appDir(), DS) . DS . '.gitignore';
        }
    }
}

<?php

class CException_Solution_SolutionProvider_MergeConflictSolutionProvider implements CException_Contract_HasSolutionsForThrowableInterface {
    public function canSolve($throwable) {
        if (!($throwable instanceof ParseError)) {
            return false;
        }

        if (!$this->hasMergeConflictExceptionMessage($throwable)) {
            return false;
        }

        $file = file_get_contents($throwable->getFile());

        if (strpos($file, '=======') === false) {
            return false;
        }

        if (strpos($file, '>>>>>>>') === false) {
            return false;
        }

        return true;
    }

    public function getSolutions($throwable): array {
        $file = file_get_contents($throwable->getFile());
        preg_match('/\>\>\>\>\>\>\> (.*?)\n/', $file, $matches);
        $source = $matches[1];

        $target = $this->getCurrentBranch(basename($throwable->getFile()));

        return [
            CException::createSolution("Merge conflict from branch '${source}' into ${target}")
                ->setSolutionDescription('You have a Git merge conflict. To undo your merge do `git reset --hard HEAD`'),
        ];
    }

    protected function getCurrentBranch($directory) {
        $branch = "'" . trim(shell_exec("cd ${directory}; git branch | grep \\* | cut -d ' ' -f2")) . "'";

        if (!isset($branch) || $branch === "''") {
            $branch = 'current branch';
        }

        return $branch;
    }

    protected function hasMergeConflictExceptionMessage($throwable) {
        // For PHP 7.x and below
        if (cstr::startsWith($throwable->getMessage(), 'syntax error, unexpected \'<<\'')) {
            return true;
        }

        // For PHP 8+
        if (cstr::startsWith($throwable->getMessage(), 'syntax error, unexpected token "<<"')) {
            return true;
        }

        return false;
    }
}

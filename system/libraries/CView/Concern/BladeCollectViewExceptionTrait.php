<?php

trait CView_Concern_BladeCollectViewExceptionTrait {
    protected $lastCompiledData = [];

    public function collectViewData($path, array $data) {
        $this->lastCompiledData[] = [
            'path' => $path,
            'compiledPath' => $this->getCompiledPath($path),
            'data' => $this->filterViewData($data),
        ];
    }

    public function filterViewData(array $data) {
        // By default, Laravel views get two shared data keys:
        // __env and app. We try to filter them out.
        return array_filter($data, function ($value, $key) {
            if ($key === 'app') {
                return !$value instanceof CApp;
            }

            return $key !== '__env';
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function getCompiledViewData($compiledPath) {
        $compiledView = $this->findCompiledView($compiledPath);

        return isset($compiledView['data']) && $compiledView['data'] !== null ? $compiledView['data'] : [];
    }

    public function getCompiledViewName($compiledPath) {
        $compiledView = $this->findCompiledView($compiledPath);

        return isset($compiledView['path']) && $compiledView['path'] !== null ? $compiledView['path'] : $compiledPath;
    }

    protected function findCompiledView($compiledPath) {
        return CCollection::make($this->lastCompiledData)
            ->first(function ($compiledData) use ($compiledPath) {
                $comparePath = $compiledData['compiledPath'];

                return realpath(dirname($comparePath)) . DIRECTORY_SEPARATOR . basename($comparePath) === $compiledPath;
            });
    }

    protected function getCompiledPath($path) {
        if ($this instanceof CView_Engine_CompilerEngine) {
            return $this->getCompiler()->getCompiledPath($path);
        }

        return $path;
    }
}

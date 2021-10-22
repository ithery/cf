<?php

class CView_Engine_BladeCompilerEngine extends CView_Engine_CompilerEngine {
    use CView_Concern_BladeCollectViewExceptionTrait;
    protected $currentPath = null;

    /**
     * Get the evaluated contents of the view.
     *
     * @param string $path
     * @param array  $data
     *
     * @return string
     */
    public function get($path, array $data = []) {
        $this->currentPath = $path;

        $this->collectViewData($path, $data);

        return parent::get($path, $data);
    }

    /**
     * Handle a view exception.
     *
     * @param \Throwable $baseException
     * @param int        $obLevel
     *
     * @throws \Throwable
     *
     * @return void
     */
    protected function handleViewException($baseException, $obLevel) {
        while (ob_get_level() > $obLevel) {
            ob_end_clean();
        }

        if ($baseException instanceof CView_Exception_ViewException) {
            throw $baseException;
        }

        $viewExceptionClass = CView_Exception_ViewException::class;

        if ($baseException instanceof CException_Contract_ProvideSolutionInterface) {
            $viewExceptionClass = CView_Exception_ViewWithSolutionException::class;
        }

        $exception = new $viewExceptionClass(
            $this->getMessage($baseException),
            0,
            1,
            $this->getCompiledViewName($baseException->getFile()),
            $this->getBladeLineNumber($baseException->getFile(), $baseException->getLine()),
            $baseException
        );

        if ($baseException instanceof CException_Contract_ProvideSolutionInterface) {
            $exception->setSolution($baseException->getSolution());
        }

        $this->modifyViewsInTrace($exception);

        $exception->setView($this->getCompiledViewName($baseException->getFile()));
        $exception->setViewData($this->getCompiledViewData($baseException->getFile()));

        throw $exception;
    }

    protected function getBladeLineNumber(string $compiledPath, int $exceptionLineNumber): int {
        $viewPath = $this->getCompiledViewName($compiledPath);

        if (!$viewPath) {
            return $exceptionLineNumber;
        }

        $sourceMapCompiler = new CView_Compiler_BladeSourceMapCompiler();

        return $sourceMapCompiler->detectLineNumber($viewPath, $exceptionLineNumber);
    }

    protected function modifyViewsInTrace(CView_Exception_ViewException $exception) {
        $trace = CCollection::make($exception->getPrevious()->getTrace())
            ->map(function ($trace) {
                if ($compiledData = $this->findCompiledView(carr::get($trace, 'file', ''))) {
                    $trace['file'] = $compiledData['path'];
                    $trace['line'] = $this->getBladeLineNumber($trace['file'], $trace['line']);
                }

                return $trace;
            })->toArray();

        $traceProperty = new ReflectionProperty('Exception', 'trace');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue($exception, $trace);
    }
}

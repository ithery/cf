<?php

class CException_Manager {
    protected $shouldDisplayException = true;

    /**
     * @var CException_Contract_SolutionProviderRepositoryInterface
     */
    protected $solutionProviderRepository;

    /**
     * @var string
     */
    protected $solutionTransformerClass = null;

    /**
     * @var CException_Contract_ContextDetectorInterface
     */
    protected $contextDetector = null;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->solutionProviderRepository = new CException_Solution_SolutionProviderRepository($this->getDefaultSolutionProviders());
        $this->contextDetector = new CException_ContextDetector();
    }

    public function setSolutionTransformerClass($solutionTransformerClass) {
        $this->solutionTransformerClass = $solutionTransformerClass;

        return $this;
    }

    protected function getDefaultSolutionProviders() {
        return [
            CException_Solution_SolutionProvider_BadMethodCallSolutionProvider::class,
            CException_Solution_SolutionProvider_MergeConflictSolutionProvider::class,
            CException_Solution_SolutionProvider_UndefinedPropertySolutionProvider::class,
        ];
    }

    public function shouldDisplayException($shouldDisplayException = true) {
        $this->shouldDisplayException = $shouldDisplayException;

        return $this;
    }

    public function addSolutionProviders(array $solutionProviders) {
        $this->solutionProviderRepository->registerSolutionProviders($solutionProviders);

        return $this;
    }

    public function useDarkMode() {
        $this->theme('dark');

        return $this;
    }

    public function theme($theme) {
        $this->config()->setOption('theme', $theme);

        return $this;
    }

    public function config() {
        return CException_Config::instance();
    }

    /**
     * @param \Throwable $throwable
     *
     * @return CException_ErrorModel
     */
    public function createErrorModel($throwable) {
        return new CException_ErrorModel(
            $throwable,
            $this->solutionProviderRepository->getSolutionsForThrowable($throwable),
            $this->solutionTransformerClass,
        );
    }

    /**
     * @param Throwable $throwable
     *
     * @return CException_Report
     */
    public function createReport($throwable) {
        $report = CException_Report::createForThrowable(
            $throwable,
            $this->contextDetector->detectCurrentContext(),
            c::docRoot(),
            CF::version(),
        );

        //return $this->applyMiddlewareToReport($report);
        return $report;
    }
}

<?php
interface CException_Contract_SolutionProviderRepositoryInterface {
    /**
     * @param mixed $solutionProviderClass
     *
     * @return self
     */
    public function registerSolutionProvider($solutionProviderClass);

    /**
     * @param string $solutionProviderClasses
     *
     * @return self
     */
    public function registerSolutionProviders(array $solutionProviderClasses);

    /**
     * @param Throwable $throwable
     *
     * @return \CException_Contract_SolutionInterface[]
     */
    public function getSolutionsForThrowable($throwable);

    /**
     * @param string $solutionClass
     *
     * @return null|array
     */
    public function getSolutionForClass($solutionClass);
}

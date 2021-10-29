<?php

class CException_Solution_SolutionProviderRepository implements CException_Contract_SolutionProviderRepositoryInterface {
    /**
     * @var CCollection
     */
    protected $solutionProviders;

    public function __construct(array $solutionProviders = []) {
        $this->solutionProviders = CCollection::make($solutionProviders);
    }

    /**
     * @param string $solutionProviderClass
     *
     * @return $this
     */
    public function registerSolutionProvider($solutionProviderClass) {
        $this->solutionProviders->push($solutionProviderClass);

        return $this;
    }

    /**
     * @param array $solutionProviderClasses
     *
     * @return $this
     */
    public function registerSolutionProviders(array $solutionProviderClasses) {
        $this->solutionProviders = $this->solutionProviders->merge($solutionProviderClasses);

        return $this;
    }

    public function getSolutionsForThrowable($throwable) {
        $solutions = [];

        if ($throwable instanceof CException_Contract_SolutionInterface) {
            $solutions[] = $throwable;
        }

        if ($throwable instanceof CException_Contract_ProvideSolutionInterface) {
            $solutions[] = $throwable->getSolution();
        }

        $providedSolutions = $this->solutionProviders
            ->filter(function ($solutionClass) {
                if (!in_array(CException_Contract_HasSolutionsForThrowableInterface::class, class_implements($solutionClass))) {
                    return false;
                }

                /*
                if (in_array($solutionClass, config('ignition.ignored_solution_providers', []))) {
                    return false;
                }
                */

                return true;
            })->map(function ($solutionClass) {
                return new $solutionClass();
            })->filter(function (CException_Contract_HasSolutionsForThrowableInterface $solutionProvider) use ($throwable) {
                try {
                    return $solutionProvider->canSolve($throwable);
                } catch (Throwable $e) {
                    return false;
                }
            })
            ->map(function (CException_Contract_HasSolutionsForThrowableInterface $solutionProvider) use ($throwable) {
                try {
                    return $solutionProvider->getSolutions($throwable);
                } catch (Throwable $e) {
                    return [];
                }
            })
            ->flatten()
            ->toArray();

        return array_merge($solutions, $providedSolutions);
    }

    public function getSolutionForClass($solutionClass) {
        if (!class_exists($solutionClass)) {
            return null;
        }

        if (!in_array(CException_Contract_SolutionInterface::class, class_implements($solutionClass))) {
            return null;
        }

        return c::container($solutionClass);
    }
}

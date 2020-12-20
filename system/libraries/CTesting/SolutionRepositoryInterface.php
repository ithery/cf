<?php

/**
 * @internal
 */
interface CTesting_SolutionRepositoryInterface {
    /**
     * Gets the solutions from the given `$throwable`.
     *
     * @param Throwable $throwable
     *
     * @return array<int, Solution>
     */
    public function getFromThrowable($throwable);
}

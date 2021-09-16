<?php

/**
 * @internal
 */
final class CTesting_SolutionRepository_NullSolutionRepository implements CTesting_SolutionRepositoryInterface {
    /**
     * {@inheritdoc}
     */
    public function getFromThrowable($throwable) {
        return [];
    }
}

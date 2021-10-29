<?php

class CException_Solution_SolutionProvider_BadMethodCallSolutionProvider implements CException_Contract_HasSolutionsForThrowableInterface {
    protected const REGEX = '/([a-zA-Z\\\\]+)::([a-zA-Z]+)/m';

    public function canSolve($throwable) {
        if (!$throwable instanceof BadMethodCallException) {
            return false;
        }

        if (is_null($this->getClassAndMethodFromExceptionMessage($throwable->getMessage()))) {
            return false;
        }

        return true;
    }

    public function getSolutions($throwable) {
        return [
            CException_Solution::create('Bad Method Call')
                ->setSolutionDescription($this->getSolutionDescription($throwable)),
        ];
    }

    /**
     * @param Throwable $throwable
     *
     * @return string
     */
    public function getSolutionDescription($throwable) {
        if (!$this->canSolve($throwable)) {
            return '';
        }

        extract($this->getClassAndMethodFromExceptionMessage($throwable->getMessage()), EXTR_OVERWRITE);

        $possibleMethod = $this->findPossibleMethod($class, $method);

        return "Did you mean {$class}::{$possibleMethod->name}() ?";
    }

    /**
     * @param string $message
     *
     * @return null|array
     */
    protected function getClassAndMethodFromExceptionMessage($message) {
        if (!preg_match(self::REGEX, $message, $matches)) {
            return null;
        }

        return [
            'class' => $matches[1],
            'method' => $matches[2],
        ];
    }

    protected function findPossibleMethod($class, $invalidMethodName) {
        return $this->getAvailableMethods($class)
            ->sortByDesc(function (ReflectionMethod $method) use ($invalidMethodName) {
                similar_text($invalidMethodName, $method->name, $percentage);

                return $percentage;
            })->first();
    }

    /**
     * @param string $class
     *
     * @return CCollection
     */
    protected function getAvailableMethods($class) {
        $class = new ReflectionClass($class);

        return CCollection::make($class->getMethods());
    }
}

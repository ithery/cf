<?php

class CException_Solution_SolutionProvider_UndefinedPropertySolutionProvider implements CException_Contract_HasSolutionsForThrowableInterface {
    protected const REGEX = '/([a-zA-Z\\\\]+)::\$([a-zA-Z]+)/m';

    protected const MINIMUM_SIMILARITY = 80;

    public function canSolve($throwable) {
        if (!$throwable instanceof ErrorException) {
            return false;
        }

        if (is_null($this->getClassAndPropertyFromExceptionMessage($throwable->getMessage()))) {
            return false;
        }

        if (!$this->similarPropertyExists($throwable)) {
            return false;
        }

        return true;
    }

    public function getSolutions($throwable) {
        return [
            CException::createSolution('Unknown Property')
                ->setSolutionDescription($this->getSolutionDescription($throwable)),
        ];
    }

    public function getSolutionDescription($throwable) {
        if (!$this->canSolve($throwable) || !$this->similarPropertyExists($throwable)) {
            return '';
        }

        extract($this->getClassAndPropertyFromExceptionMessage($throwable->getMessage()), EXTR_OVERWRITE);

        $possibleProperty = $this->findPossibleProperty($class, $property);

        return "Did you mean {$class}::\${$possibleProperty->name} ?";
    }

    protected function similarPropertyExists($throwable) {
        extract($this->getClassAndPropertyFromExceptionMessage($throwable->getMessage()), EXTR_OVERWRITE);

        $possibleProperty = $this->findPossibleProperty($class, $property);

        return $possibleProperty !== null;
    }

    protected function getClassAndPropertyFromExceptionMessage($message) {
        if (!preg_match(self::REGEX, $message, $matches)) {
            return null;
        }

        return [
            'class' => $matches[1],
            'property' => $matches[2],
        ];
    }

    protected function findPossibleProperty($class, $invalidPropertyName) {
        return $this->getAvailableProperties($class)
            ->sortByDesc(function (ReflectionProperty $property) use ($invalidPropertyName) {
                similar_text($invalidPropertyName, $property->name, $percentage);

                return $percentage;
            })
            ->filter(function (ReflectionProperty $property) use ($invalidPropertyName) {
                similar_text($invalidPropertyName, $property->name, $percentage);

                return $percentage >= self::MINIMUM_SIMILARITY;
            })->first();
    }

    protected function getAvailableProperties($class) {
        $class = new ReflectionClass($class);

        return CCollection::make($class->getProperties());
    }
}

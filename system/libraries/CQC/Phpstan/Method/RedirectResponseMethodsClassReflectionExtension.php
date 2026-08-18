<?php

use PHPStan\Reflection;

class CQC_Phpstan_Method_RedirectResponseMethodsClassReflectionExtension implements Reflection\MethodsClassReflectionExtension {
    public function hasMethod(Reflection\ClassReflection $classReflection, string $methodName): bool {
        if ($classReflection->getName() !== 'Illuminate\Http\RedirectResponse') {
            return false;
        }

        if (!str_starts_with($methodName, 'with')) {
            return false;
        }

        return true;
    }

    public function getMethod(
        Reflection\ClassReflection $classReflection,
        string $methodName
    ): Reflection\MethodReflection {
        return new CQC_Phpstan_Reflection_DynamicWhereMethodReflection($classReflection, $methodName);
    }
}

<?php
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SebastianBergmann\Type;

use function substr;
use ReflectionClass;
use function explode;
use function implode;
use function array_pop;

final class TypeName {
    private $namespaceName;

    private $simpleName;

    public static function fromQualifiedName($fullClassName) {
        if ($fullClassName[0] === '\\') {
            $fullClassName = substr($fullClassName, 1);
        }

        $classNameParts = explode('\\', $fullClassName);

        $simpleName = array_pop($classNameParts);
        $namespaceName = implode('\\', $classNameParts);

        return new self($namespaceName, $simpleName);
    }

    public static function fromReflection(ReflectionClass $type) {
        return new self(
            $type->getNamespaceName(),
            $type->getShortName()
        );
    }

    public function __construct($namespaceName, $simpleName) {
        if ($namespaceName === '') {
            $namespaceName = null;
        }

        $this->namespaceName = $namespaceName;
        $this->simpleName = $simpleName;
    }

    public function namespaceName() {
        return $this->namespaceName;
    }

    public function simpleName() {
        return $this->simpleName;
    }

    public function qualifiedName() {
        return $this->namespaceName === null
             ? $this->simpleName
             : $this->namespaceName . '\\' . $this->simpleName;
    }

    public function isNamespaced() {
        return $this->namespaceName !== null;
    }
}

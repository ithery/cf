<?php

namespace League\Flysystem;

final class PortableVisibilityGuard {
    /**
     * @param string $visibility
     *
     * @return void
     */
    public static function guardAgainstInvalidInput($visibility) {
        if ($visibility !== Visibility::VISIBILITY_PUBLIC && $visibility !== Visibility::VISIBILITY_PRIVATE) {
            $className = Visibility::class;

            throw InvalidVisibilityProvided::withVisibility(
                $visibility,
                "either {$className}::PUBLIC or {$className}::PRIVATE"
            );
        }
    }
}

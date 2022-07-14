<?php

namespace League\Flysystem\UnixVisibility;

use League\Flysystem\Visibility;
use League\Flysystem\PortableVisibilityGuard;

class PortableVisibilityConverter implements VisibilityConverter {
    /**
     * @var int
     */
    private $filePublic;

    /**
     * @var int
     */
    private $filePrivate;

    /**
     * @var int
     */
    private $directoryPublic;

    /**
     * @var int
     */
    private $directoryPrivate;

    /**
     * @var string
     */
    private $defaultForDirectories;

    public function __construct(
        $filePublic = 0644,
        $filePrivate = 0600,
        $directoryPublic = 0755,
        $directoryPrivate = 0700,
        $defaultForDirectories = Visibility::VISIBILITY_PRIVATE
    ) {
        $this->filePublic = $filePublic;
        $this->filePrivate = $filePrivate;
        $this->directoryPublic = $directoryPublic;
        $this->directoryPrivate = $directoryPrivate;
        $this->defaultForDirectories = $defaultForDirectories;
    }

    /**
     * @param string $visibility
     *
     * @return int
     */
    public function forFile($visibility) {
        PortableVisibilityGuard::guardAgainstInvalidInput($visibility);

        return $visibility === Visibility::VISIBILITY_PUBLIC
            ? $this->filePublic
            : $this->filePrivate;
    }

    /**
     * @param string $visibility
     *
     * @return int
     */
    public function forDirectory($visibility) {
        PortableVisibilityGuard::guardAgainstInvalidInput($visibility);

        return $visibility === Visibility::VISIBILITY_PUBLIC
            ? $this->directoryPublic
            : $this->directoryPrivate;
    }

    /**
     * @param int $visibility
     *
     * @return string
     */
    public function inverseForFile($visibility) {
        if ($visibility === $this->filePublic) {
            return Visibility::VISIBILITY_PUBLIC;
        } elseif ($visibility === $this->filePrivate) {
            return Visibility::VISIBILITY_PRIVATE;
        }

        return Visibility::VISIBILITY_PUBLIC; // default
    }

    /**
     * @param int $visibility
     *
     * @return string
     */
    public function inverseForDirectory($visibility) {
        if ($visibility === $this->directoryPublic) {
            return Visibility::VISIBILITY_PUBLIC;
        } elseif ($visibility === $this->directoryPrivate) {
            return Visibility::VISIBILITY_PRIVATE;
        }

        return Visibility::VISIBILITY_PUBLIC; // default
    }

    /**
     * @return int
     */
    public function defaultForDirectories() {
        return $this->defaultForDirectories === Visibility::VISIBILITY_PUBLIC ? $this->directoryPublic : $this->directoryPrivate;
    }

    /**
     * @param array<mixed> $permissionMap
     * @param mixed        $defaultForDirectories
     *
     * @return PortableVisibilityConverter
     */
    public static function fromArray(array $permissionMap, $defaultForDirectories = Visibility::VISIBILITY_PRIVATE) {
        return new PortableVisibilityConverter(
            isset($permissionMap['file']['public']) ? $permissionMap['file']['public'] : 0644,
            isset($permissionMap['file']['private']) ? $permissionMap['file']['private'] : 0600,
            isset($permissionMap['dir']['public']) ? $permissionMap['dir']['public'] : 0755,
            isset($permissionMap['dir']['private']) ? $permissionMap['dir']['private'] : 0700,
            $defaultForDirectories
        );
    }
}

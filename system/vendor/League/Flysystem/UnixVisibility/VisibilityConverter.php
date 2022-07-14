<?php

namespace League\Flysystem\UnixVisibility;

interface VisibilityConverter {
    public function forFile($visibility);

    public function forDirectory($visibility);

    public function inverseForFile($visibility);

    public function inverseForDirectory($visibility);

    public function defaultForDirectories();
}

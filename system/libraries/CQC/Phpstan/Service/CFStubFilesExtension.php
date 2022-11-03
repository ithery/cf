<?php

use Symfony\Component\Finder\Finder;
use PHPStan\PhpDoc\StubFilesExtension;

final class CQC_Phpstan_Service_CFStubFilesExtension implements StubFilesExtension {
    /**
     * @inheritDoc
     */
    public function getFiles(): array {
        $files = [];

        $finder = Finder::create()->files()->name('*.stub')->in(__DIR__ . '/../../../../stubs/phpstan');

        foreach ($finder as $file) {
            $files[] = $file->getPathname();
        }

        return $files;
    }
}

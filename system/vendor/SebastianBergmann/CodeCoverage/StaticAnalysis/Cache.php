<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use const DIRECTORY_SEPARATOR;
use function file_get_contents;
use function file_put_contents;
use function filemtime;
use function hash;
use function is_file;
use function serialize;
use function unserialize;
use SebastianBergmann\CodeCoverage\Directory;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class Cache
{
    /**
     * @var string
     */
    private $directory;

    public function __construct($directory)
    {
        Directory::create($directory);

        $this->directory = $directory;
    }

    protected function has($filename, $key)
    {
        $cacheFile = $this->cacheFile($filename, $key);

        if (!is_file($cacheFile)) {
            return false;
        }

        if (filemtime($cacheFile) < filemtime($filename)) {
            return false;
        }

        return true;
    }

    /**
     * @psalm-param list<class-string> $allowedClasses
     *
     * @return mixed
     */
    protected function read($filename, $key, array $allowedClasses = [])
    {
        $options = ['allowed_classes' => false];

        if (!empty($allowedClasses)) {
            $options = ['allowed_classes' => $allowedClasses];
        }

        return unserialize(
            file_get_contents(
                $this->cacheFile($filename, $key)
            ),
            $options
        );
    }

    /**
     * @param mixed $data
     */
    protected function write($filename, $key, $data)
    {
        file_put_contents(
            $this->cacheFile($filename, $key),
            serialize($data)
        );
    }

    private function cacheFile($filename, $key)
    {
        return $this->directory . DIRECTORY_SEPARATOR . hash('sha256', $filename . $key);
    }
}

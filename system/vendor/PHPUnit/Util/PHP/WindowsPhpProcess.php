<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\PHP;

use const PHP_MAJOR_VERSION;
use function tmpfile;
use PHPUnit\Framework\Exception\Exception;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 *
 * @see https://bugs.php.net/bug.php?id=51800
 */
final class WindowsPhpProcess extends DefaultPhpProcess
{
    public function getCommand(array $settings, $file = null)
    {
        if (PHP_MAJOR_VERSION < 8) {
            return '"' . parent::getCommand($settings, $file) . '"';
        }

        return parent::getCommand($settings, $file);
    }

    /**
     * @throws Exception
     */
    protected function getHandles()
    {
        if (false === $stdout_handle = tmpfile()) {
            throw new Exception(
                'A temporary file could not be created; verify that your TEMP environment variable is writable'
            );
        }

        return [
            1 => $stdout_handle,
        ];
    }

    protected function useTemporaryFile()
    {
        return true;
    }
}

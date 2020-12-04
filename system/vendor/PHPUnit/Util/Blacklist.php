<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util;

/**
 * @deprecated Use ExcludeList instead
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class Blacklist
{
    public static function addDirectory($directory)
    {
        ExcludeList::addDirectory($directory);
    }

    /**
     * @throws Exception
     *
     * @return string[]
     */
    public function getBlacklistedDirectories()
    {
        return (new ExcludeList)->getExcludedDirectories();
    }

    /**
     * @throws Exception
     */
    public function isBlacklisted($file)
    {
        return (new ExcludeList)->isExcluded($file);
    }
}

<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PHPUnit\TextUI\XmlConfiguration\Migration;

use function array_key_exists;
use function sprintf;
use function version_compare;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class MigrationBuilder {
    const AVAILABLE_MIGRATIONS = [
        '8.5' => [
            RemoveLogTypes::class,
        ],

        '9.2' => [
            RemoveCacheTokensAttribute::class,
            IntroduceCoverageElement::class,
            MoveAttributesFromRootToCoverage::class,
            MoveAttributesFromFilterWhitelistToCoverage::class,
            MoveWhitelistDirectoriesToCoverage::class,
            MoveWhitelistExcludesToCoverage::class,
            RemoveEmptyFilter::class,
            CoverageCloverToReport::class,
            CoverageCrap4jToReport::class,
            CoverageHtmlToReport::class,
            CoveragePhpToReport::class,
            CoverageTextToReport::class,
            CoverageXmlToReport::class,
            ConvertLogTypes::class,
            UpdateSchemaLocationTo93::class,
        ],
    ];

    /**
     * @throws MigrationBuilderException
     *
     * @param mixed $fromVersion
     */
    public function build($fromVersion) {
        if (!array_key_exists($fromVersion, self::AVAILABLE_MIGRATIONS)) {
            throw new MigrationBuilderException(
                sprintf(
                    'Migration from schema version %s is not supported',
                    $fromVersion
                )
            );
        }

        $stack = [];

        foreach (self::AVAILABLE_MIGRATIONS as $version => $migrations) {
            if (version_compare($version, $fromVersion, '<')) {
                continue;
            }

            foreach ($migrations as $migration) {
                $stack[] = new $migration;
            }
        }

        return $stack;
    }
}

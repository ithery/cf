<?php
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI\XmlConfiguration;

use DOMElement;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class CoverageTextToReport extends LogToReportMigration
{
    protected function forType()
    {
        return 'coverage-text';
    }

    protected function toReportFormat(DOMElement $logNode)
    {
        $text = $logNode->ownerDocument->createElement('text');
        $text->setAttribute('outputFile', $logNode->getAttribute('target'));

        $this->migrateAttributes($logNode, $text, ['showUncoveredFiles', 'showOnlySummary']);

        return $text;
    }
}

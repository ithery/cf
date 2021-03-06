<?php

/*
 * This is part of the league/commonmark package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Table;

use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\Environment\ConfigurableEnvironmentInterface;

final class TableExtension implements ExtensionInterface {
    public function register(ConfigurableEnvironmentInterface $environment) {
        $environment
            ->addBlockStartParser(new TableStartParser())

            ->addRenderer(Table::class, new TableRenderer())
            ->addRenderer(TableSection::class, new TableSectionRenderer())
            ->addRenderer(TableRow::class, new TableRowRenderer())
            ->addRenderer(TableCell::class, new TableCellRenderer());
    }
}

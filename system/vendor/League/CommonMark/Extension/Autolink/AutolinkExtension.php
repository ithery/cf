<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\Autolink;

use League\CommonMark\Environment\ConfigurableEnvironmentInterface;
use League\CommonMark\Extension\ExtensionInterface;

final class AutolinkExtension implements ExtensionInterface {
    public function register(ConfigurableEnvironmentInterface $environment) {
        $environment->addInlineParser(new EmailAutolinkParser());
        $environment->addInlineParser(new UrlAutolinkParser());
    }
}

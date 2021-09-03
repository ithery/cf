<?php
/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark;

use League\CommonMark\Output\RenderedContentInterface;

/**
 * Interface for a service which converts Markdown to HTML.
 */
interface MarkdownConverterInterface {
    /**
     * Converts Markdown to HTML.
     *
     * @throws \RuntimeException
     *
     * @param string $markdown
     */
    public function convertToHtml($markdown);
}

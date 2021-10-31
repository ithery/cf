<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Renderer;

use League\CommonMark\Node\Block\Document;
use League\CommonMark\Output\RenderedContentInterface;

/**
 * Renders a parsed Document AST to HTML
 */
interface HtmlRendererInterface {
    /**
     * Render the given Document node (and all of its children)
     *
     * @return RenderedContentInterface
     */
    public function renderDocument(Document $node);
}

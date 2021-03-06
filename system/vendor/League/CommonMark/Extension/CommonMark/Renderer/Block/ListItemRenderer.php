<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\CommonMark\Renderer\Block;

use League\CommonMark\Extension\CommonMark\Node\Block\ListItem;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

final class ListItemRenderer implements NodeRendererInterface {
    /**
     * @param ListItem $node
     *
     * {@inheritdoc}
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer) {
        if (!($node instanceof ListItem)) {
            throw new \InvalidArgumentException('Incompatible node type: ' . \get_class($node));
        }

        $contents = $childRenderer->renderNodes($node->children());
        if (\substr($contents, 0, 1) === '<' && !$this->startsTaskListItem($node)) {
            $contents = "\n" . $contents;
        }

        if (\substr($contents, -1, 1) === '>') {
            $contents .= "\n";
        }

        $attrs = $node->data->get('attributes');

        return new HtmlElement('li', $attrs, $contents);
    }

    private function startsTaskListItem(ListItem $block) {
        $firstChild = $block->firstChild();

        return $firstChild instanceof Paragraph && $firstChild->firstChild() instanceof TaskListItemMarker;
    }
}

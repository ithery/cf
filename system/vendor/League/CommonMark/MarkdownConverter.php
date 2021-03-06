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

use League\CommonMark\Environment\EnvironmentInterface;
use League\CommonMark\Output\RenderedContentInterface;
use League\CommonMark\Parser\MarkdownParser;
use League\CommonMark\Parser\MarkdownParserInterface;
use League\CommonMark\Renderer\HtmlRenderer;

class MarkdownConverter implements MarkdownConverterInterface {
    /**
     * @var EnvironmentInterface
     */
    private $environment;

    /**
     * @var MarkdownParserInterface
     */
    private $markdownParser;

    /**
     * @var HtmlRenderer
     */
    private $htmlRenderer;

    public function __construct(EnvironmentInterface $environment) {
        $this->environment = $environment;
        $this->markdownParser = new MarkdownParser($environment);
        $this->htmlRenderer = new HtmlRenderer($environment);
    }

    /**
     * @return EnvironmentInterface
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $markdown The Markdown to convert
     *
     * @return RenderedContentInterface Rendered HTML
     *
     * @throws \RuntimeException
     */
    public function convertToHtml($markdown) {
        $documentAST = $this->markdownParser->parse($markdown);

        return $this->htmlRenderer->renderDocument($documentAST);
    }

    /**
     * Converts CommonMark to HTML.
     *
     * @param string $markdown
     *
     * @see Converter::convertToHtml
     *
     * @return RenderedContentInterface
     *
     * @throws \RuntimeException
     */
    public function __invoke($markdown) {
        return $this->convertToHtml($markdown);
    }
}

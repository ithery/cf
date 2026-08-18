<?php

declare(strict_types=1);

namespace Termwind;

use DOMDocument;
use DOMNode;
use Termwind\Html\CodeRenderer;
use Termwind\Html\PreRenderer;
use Termwind\Html\TableRenderer;
use Termwind\ValueObjects\Node;

/**
 * @internal
 */
final class HtmlRenderer
{
    /**
     * Renders the given html.
     */
    public function render(string $html, int $options): void
    {
        $this->parse($html)->render($options);
    }

    /**
     * Parses the given html.
     */
    public function parse(string $html): Components\Element
    {
        $dom = new DOMDocument();

        if (strip_tags($html) === $html) {
            return Termwind::span($html);
        }

        $html = '<?xml encoding="UTF-8">'.trim($html);
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_COMPACT | LIBXML_HTML_NODEFDTD | LIBXML_NOBLANKS | LIBXML_NOXMLDECL);

        /** @var DOMNode $body */
        $body = $dom->getElementsByTagName('body')->item(0);
        $el = $this->convert(new Node($body));

        // @codeCoverageIgnoreStart
        return is_string($el)
            ? Termwind::span($el)
            : $el;
        // @codeCoverageIgnoreEnd
    }

    /**
     * Convert a tree of DOM nodes to a tree of termwind elements.
     */
    private function convert(Node $node)
    {
        $children = [];

        if ($node->isName('table')) {
            return (new TableRenderer)->toElement($node);
        } elseif ($node->isName('code')) {
            return (new CodeRenderer)->toElement($node);
        } elseif ($node->isName('pre')) {
            return (new PreRenderer)->toElement($node);
        }

        foreach ($node->getChildNodes() as $child) {
            $children[] = $this->convert($child);
        }

        $children = array_filter($children, fn ($child) => $child !== '');

        return $this->toElement($node, $children);
    }

    /**
     * Convert a given DOM node to it's termwind element equivalent.
     *
     * @param  array<int, Components\Element|string>  $children
     */
    private function toElement(Node $node, array $children)
    {
        if ($node->isText() || $node->isComment()) {
            return (string) $node;
        }

        /** @var array<string, mixed> $properties */
        $properties = [
            'isFirstChild' => $node->isFirstChild(),
        ];

        $styles = $node->getClassAttribute();

        $nodeName = $node->getName();
        switch ($nodeName) {
            case 'body':
                $result = $children[0]; // Pick only the first element from the body node
                break;
            case 'div':
                $result = Termwind::div($children, $styles, $properties);
                break;
            case 'p':
                $result = Termwind::paragraph($children, $styles, $properties);
                break;
            case 'ul':
                $result = Termwind::ul($children, $styles, $properties);
                break;
            case 'ol':
                $result = Termwind::ol($children, $styles, $properties);
                break;
            case 'li':
                $result = Termwind::li($children, $styles, $properties);
                break;
            case 'dl':
                $result = Termwind::dl($children, $styles, $properties);
                break;
            case 'dt':
                $result = Termwind::dt($children, $styles, $properties);
                break;
            case 'dd':
                $result = Termwind::dd($children, $styles, $properties);
                break;
            case 'span':
                $result = Termwind::span($children, $styles, $properties);
                break;
            case 'br':
                $result = Termwind::breakLine($styles, $properties);
                break;
            case 'strong':
                $result = Termwind::span($children, $styles, $properties)->strong();
                break;
            case 'b':
                $result = Termwind::span($children, $styles, $properties)->fontBold();
                break;
            case 'em':
            case 'i':
                $result = Termwind::span($children, $styles, $properties)->italic();
                break;
            case 'u':
                $result = Termwind::span($children, $styles, $properties)->underline();
                break;
            case 's':
                $result = Termwind::span($children, $styles, $properties)->lineThrough();
                break;
            case 'a':
                $result = Termwind::anchor($children, $styles, $properties)->href($node->getAttribute('href'));
                break;
            case 'hr':
                $result = Termwind::hr($styles, $properties);
                break;
            default:
                $result = Termwind::div($children, $styles, $properties);
                break;
        }
        return $result;
    }
}

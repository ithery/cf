<?php
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeCoverage\StaticAnalysis;

use PhpParser\Error;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class ParsingUncoveredFileAnalyser implements UncoveredFileAnalyser
{
    public function executableLinesIn($filename)
    {
        $parser = (new ParserFactory)->create(
            ParserFactory::PREFER_PHP7,
            new Lexer
        );

        try {
            $nodes = $parser->parse(file_get_contents($filename));

            assert($nodes !== null);

            $traverser = new NodeTraverser;
            $visitor   = new ExecutableLinesFindingVisitor;

            $traverser->addVisitor($visitor);

            /* @noinspection UnusedFunctionResultInspection */
            $traverser->traverse($nodes);

            return $visitor->executableLines();

            // @codeCoverageIgnoreStart
        } catch (Error $error) {
        }
        // @codeCoverageIgnoreEnd

        return [];
    }
}

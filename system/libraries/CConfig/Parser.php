<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 17, 2019, 3:35:10 AM
 */
use PhpParser\Node;
use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\NodeFinder;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

class CConfig_Parser {
    /**
     * @param string $file
     * @param string $key
     *
     * @return string
     */
    public function getComment($file, $key) {
        $code = file_get_contents($file);
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $comment = '';

        try {
            $ast = $parser->parse($code);
            $traverser = new NodeTraverser();
            $visitor = new CConfig_Parser_CommentVisitor($key);
            $traverser->addVisitor($visitor);
            $modifiedAst = $traverser->traverse($ast);
            $comment = $visitor->getComment();
        } catch (Error $error) {
            //echo "Parse error: {$error->getMessage()}\n";
            return '';
        }

        return $comment;
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 17, 2019, 4:23:10 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;

class CConfig_Parser_CommentVisitor extends NodeVisitorAbstract {

    protected $configKey;
    protected $currentKeyParts;
    protected $comment;

    public function __construct($key) {
        $this->configKey = $key;
        $this->currentKeyParts = array();
        $this->comment = null;
    }

    public function enterNode(Node $node) {
        if ($node instanceof Node\Expr\ArrayItem) {
            $nodeKey = $node->key;

            if ($nodeKey instanceof Node\Scalar\String_) {
                $nodeKeyValue = $nodeKey->value;
                $this->currentKeyParts[] = $nodeKeyValue;

                if ($this->currentKey() == $this->configKey) {
                    $comments = $nodeKey->getAttribute('comments');
                    if(is_array($comments)) {
                        $comments = implode("\r\n", $comments);
                    }
                    if(strlen($comments)>0) {
                        $this->comment = $comments;
                    }
                    return NodeTraverser::STOP_TRAVERSAL;
                }
            }
        }
    }

    public function leaveNode(Node $node) {
        if ($node instanceof Node\Expr\ArrayItem) {
            $nodeKey = $node->key;
            if ($nodeKey instanceof Node\Scalar\String_) {
                array_pop($this->currentKeyParts);
            }
        }
    }

    protected function currentKey() {
        return implode(".", $this->currentKeyParts);
    }

    public function getComment() {
        return $this->comment;
    }

}

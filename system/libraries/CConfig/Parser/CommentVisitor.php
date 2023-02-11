<?php

defined('SYSPATH') or die('No direct access allowed.');

use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;

class CConfig_Parser_CommentVisitor extends NodeVisitorAbstract {
    protected $configKey;

    protected $currentKeyParts;

    protected $comment;

    public function __construct($key) {
        $this->configKey = $key;
        $this->currentKeyParts = [];
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
                    if (is_array($comments)) {
                        $comments = implode("\r\n", $comments);
                    }
                    if (strlen($comments) > 0) {
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
        return implode('.', $this->currentKeyParts);
    }

    public function getComment() {
        return $this->comment;
    }
}

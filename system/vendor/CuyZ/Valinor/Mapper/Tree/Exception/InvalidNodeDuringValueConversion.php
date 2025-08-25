<?php

declare(strict_types=1);

namespace CuyZ\Valinor\Mapper\Tree\Exception;

use CuyZ\Valinor\Mapper\Tree\Builder\Node;
use Exception;

/** @internal */
final class InvalidNodeDuringValueConversion extends Exception
{
    public Node $node;
    public function __construct(
        Node $node
    ) {
        $this->node = $node;
        // @infection-ignore-all
        parent::__construct();
    }
}

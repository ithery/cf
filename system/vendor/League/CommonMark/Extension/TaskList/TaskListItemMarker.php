<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Extension\TaskList;

use League\CommonMark\Node\Inline\AbstractInline;

final class TaskListItemMarker extends AbstractInline {
    /**
     * @var bool
     *
     * @psalm-readonly-allow-private-mutation
     */
    private $checked;

    public function __construct($isCompleted) {
        parent::__construct();

        $this->checked = $isCompleted;
    }

    public function isChecked() {
        return $this->checked;
    }

    public function setChecked($checked) {
        $this->checked = $checked;
    }
}

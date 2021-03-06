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

namespace League\CommonMark\Extension\CommonMark\Node\Inline;

use League\CommonMark\Node\Inline\AbstractInline;

abstract class AbstractWebResource extends AbstractInline {
    /**
     * @var string
     */
    protected $url;

    public function __construct($url) {
        parent::__construct();

        $this->url = $url;
    }

    public function getUrl() {
        return $this->url;
    }

    public function setUrl($url) {
        $this->url = $url;
    }
}

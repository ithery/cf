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

use League\CommonMark\Node\Inline\Text;

class Image extends AbstractWebResource {
    public function __construct($url, $label = null, $title = null) {
        parent::__construct($url);

        if ($label !== null && $label !== '') {
            $this->appendChild(new Text($label));
        }

        if ($title !== null && $title !== '') {
            $this->data->set('title', $title);
        }
    }
}

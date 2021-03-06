<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mailer\Bridge\Postmark\Transport;

use Symfony\Component\Mime\Header\UnstructuredHeader;

final class MessageStreamHeader extends UnstructuredHeader
{
    public function __construct(string $value)
    {
        parent::__construct('X-PM-Message-Stream', $value);
    }
}

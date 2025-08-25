<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database\Query\Filter;

use Beste\Json;
use Kreait\Firebase\Database\Query\Filter;
use Kreait\Firebase\Database\Query\ModifierTrait;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
final class EndAt implements Filter
{
    use ModifierTrait;

    /**
     * @var bool|float|int|string
     */
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'endAt', Json::encode($this->value));
    }
}

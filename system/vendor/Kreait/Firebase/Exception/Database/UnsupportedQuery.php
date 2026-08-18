<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Database;

use Kreait\Firebase\Database\Query;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Exception\RuntimeException;
use Throwable;

final class UnsupportedQuery extends RuntimeException implements DatabaseException
{
    private Query $query;
    public function __construct(
        Query $query,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $this->query = $query;
        parent::__construct($message, $code, $previous);
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}

<?php

class CModel_QuerySerializer_Query {
    /**
     * Serialize to basic Query Builder.
     */
    public static function serialize(CDatabase_Query_Builder $builder): array {
        /** @var string|CDatabase $connection */
        $connection = $builder->getConnection();

        return array_filter([
            'connection' => \is_string($connection) ? $connection : $connection->getName(),
            'columns' => $builder->columns,
            'bindings' => $builder->bindings,
            'distinct' => $builder->distinct,
            'from' => $builder->from,
            'joins' => c::collect($builder->joins)->map(static function ($join) {
                return CModel_QuerySerializer_JoinClause::serialize($join);
            })->all(),
            'wheres' => c::collect($builder->wheres)->map(static function ($where) {
                if (isset($where['query'])) {
                    $where['query'] = static::serialize($where['query']);
                }

                return $where;
            })->all(),
            'groups' => $builder->groups,
            'havings' => $builder->havings,
            'orders' => $builder->orders,
            'limit' => $builder->limit,
            'offset' => $builder->offset,
            'unions' => $builder->unions,
            'unionLimit' => $builder->unionLimit,
            'unionOrders' => $builder->unionOrders,
            'lock' => $builder->lock,
        ]);
    }

    /**
     * Unserialize to basic Query Builder.
     */
    public static function unserialize(array $payload) {
        $connection = $payload['connection'] ?? null;

        unset($payload['connection']);

        return static::unserializeFor(CDatabase::instance($connection)->newQuery(), $payload);
    }

    /**
     * Unserialize for basic Query Builder.
     */
    public static function unserializeFor(CDatabase_Query_Builder $builder, array $payload) {
        c::collect($payload)->transform(static function ($value, $type) use ($builder) {
            if ($type === 'wheres') {
                foreach ($value as $index => $where) {
                    if (isset($where['query']) && \is_array($where['query'])) {
                        $value[$index]['query'] = static::unserialize($where['query']);
                    }
                }
            }

            if ($type === 'joins') {
                $value = CModel_QuerySerializer_JoinClause::unserialize($builder, $value ?? []);
            }

            return $value;
        })->each(static function ($value, $type) use ($builder) {
            $builder->{$type} = $value;
        });

        return $builder;
    }
}

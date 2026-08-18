<?php

trait CModel_Relation_Trait_HasEagerLimit {
    /**
     * Alias to set the "limit" value of the query.
     *
     * @param int $value
     *
     * @return $this
     */
    public function take($value) {
        return $this->limit($value);
    }

    /**
     * Set the "limit" value of the query.
     *
     * @param int $value
     *
     * @return $this
     */
    public function limit($value) {
        if ($this->farParent->exists) {
            $this->query->limit($value);
        } else {
            if (!class_exists('Staudenmeir\EloquentEagerLimit\Builder')) {
                $message = 'Please install staudenmeir/eloquent-eager-limit and add the HasEagerLimit trait as shown in the README.'; // @codeCoverageIgnore

                throw new RuntimeException($message); // @codeCoverageIgnore
            }

            $column = $this->getQualifiedFirstKeyName();

            $grammar = $this->query->getQuery()->getGrammar();

            if ($grammar instanceof CDatabase_Query_Grammar_MySqlGrammar && $grammar->useLegacyGroupLimit($this->query->getQuery())) {
                $column = 'laravel_through_key';
            }

            $this->query->groupLimit($value, $column);
        }

        return $this;
    }
}

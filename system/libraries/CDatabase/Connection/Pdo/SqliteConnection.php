<?php

class CDatabase_Connection_Pdo_SqliteConnection extends CDatabase_Connection {
    /**
     * Escape a binary value for safe SQL embedding.
     *
     * @param string $value
     *
     * @return string
     */
    protected function escapeBinary($value) {
        $hex = bin2hex($value);

        return "x'{$hex}'";
    }

    /**
     * Get the default query grammar instance.
     *
     * @return \CDatabase_Query_Grammar_Sqlite
     */
    protected function getDefaultQueryGrammar() {
        ($grammar = new CDatabase_Query_Grammar_Sqlite())->setConnection($this);

        return $this->withTablePrefix($grammar);
    }
}

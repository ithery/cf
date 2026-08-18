<?php

class CDatabase_Type_MultiLineStringType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform): string {
        // @codeCoverageIgnoreStart
        return 'multilinestring';
        // @codeCoverageIgnoreEnd
    }

    public function getName(): string {
        return 'multilinestring';
    }
}

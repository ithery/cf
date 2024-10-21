<?php

class CDatabase_Type_MultiPointType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform): string {
        // @codeCoverageIgnoreStart
        return 'multipoint';
        // @codeCoverageIgnoreEnd
    }

    public function getName(): string {
        return 'multipoint';
    }
}

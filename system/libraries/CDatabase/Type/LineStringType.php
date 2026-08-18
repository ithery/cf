<?php

class CDatabase_Type_LineStringType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform): string {
        // @codeCoverageIgnoreStart
        return 'linestring';
        // @codeCoverageIgnoreEnd
    }

    public function getName(): string {
        return 'linestring';
    }
}

<?php

class CDatabase_Type_PolygonType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform): string {
        // @codeCoverageIgnoreStart
        return 'polygon';
        // @codeCoverageIgnoreEnd
    }

    public function getName(): string {
        return 'polygon';
    }
}

<?php

class CDatabase_Type_MultiPolygonType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform) {
        // @codeCoverageIgnoreStart
        return 'multipolygon';
        // @codeCoverageIgnoreEnd
    }

    public function getName() {
        return 'multipolygon';
    }
}

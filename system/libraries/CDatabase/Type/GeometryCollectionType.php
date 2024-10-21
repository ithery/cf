<?php

class CDatabase_Type_GeometryCollectionType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform) {
        // @codeCoverageIgnoreStart
        return 'geometrycollection';
        // @codeCoverageIgnoreEnd
    }

    public function getName() {
        return 'geometrycollection';
    }
}

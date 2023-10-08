<?php

class CDatabase_Type_GeometryType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform) {
        // @codeCoverageIgnoreStart
        return 'geometry';
        // @codeCoverageIgnoreEnd
    }

    public function getName() {
        return 'geometry';
    }
}

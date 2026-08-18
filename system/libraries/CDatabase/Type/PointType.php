<?php

class CDatabase_Type_PointType extends CDatabase_Type {
    public function getSQLDeclaration(array $column, CDatabase_Platform $platform) : string {
        // @codeCoverageIgnoreStart
        return 'point';
        // @codeCoverageIgnoreEnd
    }

    public function getName() : string {
        return 'point';
    }
}

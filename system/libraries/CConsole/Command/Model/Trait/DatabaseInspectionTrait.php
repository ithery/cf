<?php

use Doctrine\DBAL\Platforms\AbstractPlatform;

trait CConsole_Command_Model_Trait_DatabaseInspectionTrait {
    /**
     * A map of database column types.
     *
     * @var array
     */
    protected $typeMappings = [
        'bit' => 'string',
        'citext' => 'string',
        'enum' => 'string',
        'geometry' => 'string',
        'geomcollection' => 'string',
        'linestring' => 'string',
        'ltree' => 'string',
        'multilinestring' => 'string',
        'multipoint' => 'string',
        'multipolygon' => 'string',
        'point' => 'string',
        'polygon' => 'string',
        'sysname' => 'string',
    ];

    /**
     * Register the custom Doctrine type mappings for inspection commands.
     *
     * @param \Doctrine\DBAL\Platforms\AbstractPlatform $platform
     *
     * @return void
     */
    protected function registerTypeMappings(AbstractPlatform $platform) {
        foreach ($this->typeMappings as $type => $value) {
            $platform->registerDoctrineTypeMapping($type, $value);
        }
    }
}

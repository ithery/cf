<?php

use WKB as geoPHPWkb;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Support\Arrayable;

abstract class CGeo_Spatial_Type_Geometry implements CModel_Contract_CastableInterface, Arrayable, CInterface_Jsonable, JsonSerializable, Stringable {
    use CTrait_Macroable;

    public $srid = 0;

    abstract public function toWkt(): string;

    abstract public function getWktData(): string;

    /**
     * @return string
     */
    public function __toString() {
        return $this->toWkt();
    }

    /**
     * @param int $options
     *
     * @throws JsonException
     *
     * @return string
     */
    public function toJson($options = 0) {
        return json_encode($this, $options | JSON_THROW_ON_ERROR);
    }

    public function toWkb() {
        $geoPHPGeometry = geoPHP::load($this->toJson());

        $sridInBinary = pack('L', $this->srid);

        // @phpstan-ignore-next-line
        $wkbWithoutSrid = (new geoPHPWkb())->write($geoPHPGeometry);

        return $sridInBinary . $wkbWithoutSrid;
    }

    /**
     * @param string $wkb
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromWkb(string $wkb) {
        $srid = substr($wkb, 0, 4);
        // @phpstan-ignore-next-line
        $srid = unpack('L', $srid)[1];

        $wkb = substr($wkb, 4);

        $geometry = CGeo_Spatial_Factory::parse($wkb);
        $geometry->srid = $srid;

        if (!($geometry instanceof CGeo_Spatial_Type_Geometry)) {
            throw new InvalidArgumentException(sprintf('Expected %s, %s given.', static::class, get_class($geometry)));
        }

        return $geometry;
    }

    /**
     * @param string $wkt
     * @param int    $srid
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromWkt($wkt, $srid = 0) {
        $geometry = CGeo_Spatial_Factory::parse($wkt);
        $geometry->srid = $srid;

        if (!($geometry instanceof CGeo_Spatial_Type_Geometry)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, get_class($geometry))
            );
        }

        return $geometry;
    }

    /**
     * @param string|\GeoJson\GeoJson $geoJson
     * @param int                     $srid
     *
     * @throws InvalidArgumentException
     *
     * @return static
     */
    public static function fromJson($geoJson, int $srid = 0) {
        $geometry = CGeo_Spatial_Factory::parse($geoJson);
        $geometry->srid = $srid;

        if (!($geometry instanceof static)) {
            throw new InvalidArgumentException(
                sprintf('Expected %s, %s given.', static::class, get_class($geometry))
            );
        }

        return $geometry;
    }

    /**
     * @param array<mixed> $geometry
     *
     * @throws JsonException
     *
     * @return static
     */
    public static function fromArray(array $geometry) {
        $geoJson = json_encode($geometry, JSON_THROW_ON_ERROR);

        return static::fromJson($geoJson);
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array {
        return $this->toArray();
    }

    /**
     * @return array{type: string, coordinates: array<mixed>}
     */
    public function toArray(): array {
        return [
            'type' => c::classBasename(static::class),
            'coordinates' => $this->getCoordinates(),
        ];
    }

    /**
     * @throws JsonException
     *
     * @return string
     */
    public function toFeatureCollectionJson(): string {
        if (static::class === CGeo_Spatial_Type_GeometryCollection::class) {
            /** @var CGeo_Spatial_Type_GeometryCollection $this */
            $geometries = $this->geometries;
        } else {
            $geometries = c::collect([$this]);
        }

        $features = $geometries->map(static function (self $geometry): array {
            return [
                'type' => 'Feature',
                'properties' => [],
                'geometry' => $geometry->toArray(),
            ];
        });

        return json_encode(
            [
                'type' => 'FeatureCollection',
                'features' => $features,
            ],
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array<mixed>
     */
    abstract public function getCoordinates(): array;

    /**
     * @param array<string> $arguments
     *
     * @return CModel_Contract_CastsAttributesInterface
     */
    public static function castUsing(array $arguments) {
        return new CGeo_Spatial_GeometryCast(static::class);
    }

    /**
     * @param CDatabase_Connection $connection
     *
     * @return CDatabase_Query_Expression
     */
    public function toSqlExpression(CDatabase_Connection $connection) {
        $wkt = $this->toWkt();

        if (!(new CGeo_Spatial_AxisOrder())->supported($connection)) {
            // @codeCoverageIgnoreStart
            return c::db()->raw("ST_GeomFromText('{$wkt}', {$this->srid})");
            // @codeCoverageIgnoreEnd
        }

        return c::db()->raw("ST_GeomFromText('{$wkt}', {$this->srid}, 'axis-order=long-lat')");
    }
}

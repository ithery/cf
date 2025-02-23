<?php

namespace GeoIO\WKT\Parser;

use GeoIO\Factory;
use GeoIO\Dimension;
use JMS\Parser\AbstractParser;
use GeoIO\WKT\Parser\Exception\ParserException;

class Parser extends AbstractParser {
    private $factory;

    private $srid;

    public function __construct(Factory $factory) {
        $this->factory = $factory;
        parent::__construct(new Lexer());
    }

    public function parse($str, $context = null) {
        try {
            return parent::parse($str, $context);
        } catch (\Exception $e) {
            throw new ParserException('Parsing failed: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function parseInternal() {
        $this->srid();

        return $this->geometry();
    }

    private function srid() {
        $this->srid = null;

        if ($this->lexer->isNext('SRID')) {
            $this->match('SRID');
            $this->match('=');

            $this->srid = $this->match('INTEGER');

            $this->match(';');
        }
    }

    private function geometry(&$dimension = null) {
        $types = [
            'POINT',
            'LINESTRING',
            'POLYGON',
            'MULTIPOINT',
            'MULTILINESTRING',
            'MULTIPOLYGON',
            'GEOMETRYCOLLECTION',
        ];

        switch ($this->matchAny($types)) {
            case 'POINT':
                return $this->point($dimension);
            case 'LINESTRING':
                return $this->lineString($dimension);
            case 'POLYGON':
                return $this->polygon($dimension);
            case 'MULTIPOINT':
                return $this->multiPoint($dimension);
            case 'MULTILINESTRING':
                return $this->multiLineString($dimension);
            case 'MULTIPOLYGON':
                return $this->multiPolygon($dimension);
            default:
                return $this->geometryCollection($dimension);
        }
    }

    private function dimension($dimension) {
        if ((Dimension::DIMENSION_4D === $dimension || null === $dimension)
            && $this->lexer->isNext('ZM')) {
            $this->match('ZM');

            return Dimension::DIMENSION_4D;
        }

        if ((Dimension::DIMENSION_3DM === $dimension || null === $dimension)
            && $this->lexer->isNext('M')) {
            $this->match('M');

            return Dimension::DIMENSION_3DM;
        }

        if ((Dimension::DIMENSION_3DZ === $dimension || null === $dimension)
            && $this->lexer->isNext('Z')) {
            $this->match('Z');

            return Dimension::DIMENSION_3DZ;
        }

        return $dimension;
    }

    private function coordinates(&$dimension = null) {
        $coordinates = [
            'x' => $this->matchAny(['FLOAT', 'INTEGER']),
            'y' => $this->matchAny(['FLOAT', 'INTEGER']),
            'z' => null,
            'm' => null
        ];

        if (Dimension::DIMENSION_3DZ === $dimension
            || Dimension::DIMENSION_4D === $dimension
            || (null === $dimension && $this->lexer->isNextAny(['FLOAT', 'INTEGER']))) {
            $coordinates['z'] = $this->matchAny(['FLOAT', 'INTEGER']);
        }

        if (Dimension::DIMENSION_3DM === $dimension
            || Dimension::DIMENSION_4D === $dimension
            || (null === $dimension && $this->lexer->isNextAny(['FLOAT', 'INTEGER']))) {
            $coordinates['m'] = $this->matchAny(['FLOAT', 'INTEGER']);
        }

        if (null === $dimension) {
            if (isset($coordinates['z'], $coordinates['m'])) {
                $dimension = Dimension::DIMENSION_4D;
            } elseif (isset($coordinates['z'])) {
                $dimension = Dimension::DIMENSION_3DZ;
            }
        }

        return $this->factory->createPoint(
            $dimension ?: Dimension::DIMENSION_2D,
            $coordinates,
            $this->srid
        );
    }

    private function point(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createPoint(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        $this->match('(');
        $point = $this->coordinates($dimension);
        $this->match(')');

        return $point;
    }

    private function lineStringText(&$dimension = null, $isLinearRing = false) {
        $this->match('(');
        $points = [];

        while (true) {
            $points[] = $this->coordinates($dimension);

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }
        $this->match(')');

        if ($isLinearRing) {
            return $this->factory->createLinearRing(
                $dimension ?: Dimension::DIMENSION_2D,
                $points,
                $this->srid
            );
        }

        return $this->factory->createLineString(
            $dimension ?: Dimension::DIMENSION_2D,
            $points,
            $this->srid
        );
    }

    private function lineString(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createLineString(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        return $this->lineStringText($dimension);
    }

    private function polygonText(&$dimension = null) {
        $this->match('(');

        $lineStrings = [];

        while (true) {
            $lineStrings[] = $this->lineStringText($dimension, true);

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }

        $this->match(')');

        return $this->factory->createPolygon(
            $dimension ?: Dimension::DIMENSION_2D,
            $lineStrings,
            $this->srid
        );
    }

    private function polygon(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createPolygon(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        return $this->polygonText($dimension);
    }

    private function multiPoint(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createMultiPoint(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        $this->match('(');

        $points = [];

        while (true) {
            $nonStandardPoint = true;

            if ($this->lexer->isNext('(')) {
                $this->match('(');
                $nonStandardPoint = false;
            }

            $points[] = $this->coordinates($dimension);

            if (!$nonStandardPoint) {
                $this->match(')');
            }

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }

        $this->match(')');

        return $this->factory->createMultiPoint(
            $dimension ?: Dimension::DIMENSION_2D,
            $points,
            $this->srid
        );
    }

    private function multiLineString(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createMultiLineString(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        $this->match('(');

        $lineStrings = [];

        while (true) {
            $lineStrings[] = $this->lineStringText($dimension);

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }

        $this->match(')');

        return $this->factory->createMultiLineString(
            $dimension ?: Dimension::DIMENSION_2D,
            $lineStrings,
            $this->srid
        );
    }

    private function multiPolygon(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createMultiPolygon(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        $this->match('(');

        $polygons = [];

        while (true) {
            $polygons[] = $this->polygonText($dimension);

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }

        $this->match(')');

        return $this->factory->createMultiPolygon(
            $dimension ?: Dimension::DIMENSION_2D,
            $polygons,
            $this->srid
        );
    }

    private function geometryCollection(&$dimension = null) {
        $dimension = $this->dimension($dimension);

        if ($this->lexer->isNext('EMPTY')) {
            $this->match('EMPTY');

            return $this->factory->createGeometryCollection(
                $dimension ?: Dimension::DIMENSION_2D,
                [],
                $this->srid
            );
        }

        $this->match('(');

        $geometries = [];

        while (true) {
            $geometries[] = $this->geometry($dimension);

            if (!$this->lexer->isNext(',')) {
                break;
            }

            $this->match(',');
        }

        $this->match(')');

        return $this->factory->createGeometryCollection(
            $dimension ?: Dimension::DIMENSION_2D,
            $geometries,
            $this->srid
        );
    }
}

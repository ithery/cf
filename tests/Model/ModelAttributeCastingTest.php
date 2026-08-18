<?php
use PHPUnit\Framework\TestCase;

class ModelAttributeCastingTest extends TestCase {
    /**
     * @return ModelCastingStub
     */
    protected function makeModel() {
        $model = new ModelCastingStub();
        $model->setDateFormat('Y-m-d H:i:s');

        return $model;
    }

    // -----------------------------------------------------------------
    // Primitive scalar casts
    // -----------------------------------------------------------------

    public function testIntCast() {
        $model = $this->makeModel();

        $model->intAttribute = '3';

        $this->assertSame(3, $model->intAttribute);
        $this->assertTrue($model->hasCast('intAttribute', 'int'));
    }

    public function testFloatAndDoubleAndRealCasts() {
        $model = $this->makeModel();

        $model->floatAttribute = '1.5';
        $model->doubleAttribute = '2.5';
        $model->realAttribute = '3.5';

        $this->assertSame(1.5, $model->floatAttribute);
        $this->assertSame(2.5, $model->doubleAttribute);
        $this->assertSame(3.5, $model->realAttribute);
    }

    public function testStringCast() {
        $model = $this->makeModel();

        $model->stringAttribute = 123;

        $this->assertSame('123', $model->stringAttribute);
    }

    public function testBoolAndBooleanCasts() {
        $model = $this->makeModel();

        $model->boolAttribute = 1;
        $model->booleanAttribute = 0;

        $this->assertTrue($model->boolAttribute);
        $this->assertFalse($model->booleanAttribute);
    }

    public function testNullValuesArePassedThroughForPrimitiveCasts() {
        $model = $this->makeModel();

        $model->intAttribute = null;

        $this->assertNull($model->intAttribute);
    }

    // -----------------------------------------------------------------
    // decimal cast
    // -----------------------------------------------------------------

    public function testDecimalCastRoundsToConfiguredScaleOnRead() {
        $model = $this->makeModel();

        $model->decimalAttribute = '12.3456';

        $this->assertSame('12.35', $model->decimalAttribute);
        // The raw stored value is left untouched by the cast.
        $this->assertSame('12.3456', $model->getAttributes()['decimalAttribute']);
    }

    // -----------------------------------------------------------------
    // array / json / object / collection casts
    // -----------------------------------------------------------------

    public function testArrayCast() {
        $model = $this->makeModel();

        $model->arrayAttribute = ['a', 'b', 'c'];

        $this->assertSame(['a', 'b', 'c'], $model->arrayAttribute);
        $this->assertIsString($model->getAttributes()['arrayAttribute']);
    }

    public function testJsonCastBehavesLikeArrayCast() {
        $model = $this->makeModel();

        $model->jsonAttribute = ['x' => 1];

        $this->assertSame(['x' => 1], $model->jsonAttribute);
    }

    public function testObjectCast() {
        $model = $this->makeModel();

        $model->objectAttribute = ['x' => 1];

        $this->assertInstanceOf(stdClass::class, $model->objectAttribute);
        $this->assertSame(1, $model->objectAttribute->x);
    }

    public function testCollectionCast() {
        $model = $this->makeModel();

        $model->collectionAttribute = ['a', 'b'];

        $this->assertInstanceOf(CCollection::class, $model->collectionAttribute);
        $this->assertSame(['a', 'b'], $model->collectionAttribute->all());
    }

    public function testArrayAttributeSupportsPartialJsonPathUpdates() {
        $model = $this->makeModel();

        $model->arrayAttribute = ['name' => 'Taylor'];
        $model->fillJsonAttribute('arrayAttribute->name', 'Otwell');

        $this->assertSame(['name' => 'Otwell'], $model->arrayAttribute);
    }

    // -----------------------------------------------------------------
    // date / datetime casts
    // -----------------------------------------------------------------

    public function testDateCastReturnsStartOfDayCarbonInstance() {
        $model = $this->makeModel();

        $model->dateAttribute = '2020-05-10 15:30:00';

        $this->assertInstanceOf(CCarbon::class, $model->dateAttribute);
        $this->assertSame('2020-05-10 00:00:00', $model->dateAttribute->format('Y-m-d H:i:s'));
    }

    public function testDatetimeCastReturnsCarbonInstance() {
        $model = $this->makeModel();

        $model->datetimeAttribute = '2020-05-10 15:30:00';

        $this->assertInstanceOf(CCarbon::class, $model->datetimeAttribute);
        $this->assertSame('2020-05-10 15:30:00', $model->datetimeAttribute->format('Y-m-d H:i:s'));
    }

    public function testImmutableDateCastReturnsImmutableInstance() {
        $model = $this->makeModel();

        $model->immutableDateAttribute = '2020-05-10 15:30:00';

        $this->assertInstanceOf(DateTimeImmutable::class, $model->immutableDateAttribute);
    }

    public function testImmutableDatetimeCastReturnsImmutableInstance() {
        $model = $this->makeModel();

        $model->immutableDatetimeAttribute = '2020-05-10 15:30:00';

        $this->assertInstanceOf(DateTimeImmutable::class, $model->immutableDatetimeAttribute);
    }

    public function testCustomDatetimeCastFormatsOnRead() {
        $model = $this->makeModel();

        $model->customDatetimeAttribute = '2020-05-10 15:30:00';

        // custom_datetime casts still yield a Carbon instance from getAttribute();
        // the custom format is only applied when serializing to array/JSON.
        $this->assertInstanceOf(CCarbon::class, $model->customDatetimeAttribute);

        $array = $model->toArray();

        $this->assertSame('2020-05-10', $array['customDatetimeAttribute']);
    }

    public function testTimestampCastReturnsUnixTimestamp() {
        $model = $this->makeModel();

        $model->timestampAttribute = '2020-05-10 15:30:00';

        $this->assertSame(
            CCarbon::createFromFormat('Y-m-d H:i:s', '2020-05-10 15:30:00')->getTimestamp(),
            $model->timestampAttribute
        );
    }

    public function testDateAttributesAreSerializedToArrayAsIsoStrings() {
        $model = $this->makeModel();

        $model->datetimeAttribute = '2020-05-10 15:30:00';

        $array = $model->toArray();

        $this->assertIsString($array['datetimeAttribute']);
        $this->assertSame(
            $model->datetimeAttribute->toJSON(),
            $array['datetimeAttribute']
        );
    }

    // -----------------------------------------------------------------
    // hasCast / getCasts
    // -----------------------------------------------------------------

    public function testHasCastReportsConfiguredCasts() {
        $model = $this->makeModel();

        $this->assertTrue($model->hasCast('intAttribute'));
        $this->assertTrue($model->hasCast('intAttribute', ['int', 'integer']));
        $this->assertFalse($model->hasCast('intAttribute', ['string']));
        $this->assertFalse($model->hasCast('nonExistentAttribute'));
    }

    public function testGetCastsIncludesPrimaryKeyCast() {
        $model = $this->makeModel();

        $casts = $model->getCasts();

        $this->assertArrayHasKey('id', $casts);
        $this->assertSame('int', $casts['id']);
    }

    // -----------------------------------------------------------------
    // Attribute::make()-based casts (Laravel 9 style)
    // -----------------------------------------------------------------

    public function testAttributeBasedCastAppliesGetAndSetCallbacks() {
        $model = new ModelAttributeCastAccessorStub();

        $model->price = '19.99';

        $this->assertSame('$19.99', $model->price);
        $this->assertSame(19.99, $model->getAttributes()['price']);
    }

    // -----------------------------------------------------------------
    // isDirty / getDirty interaction with casts
    // -----------------------------------------------------------------

    public function testFloatCastUsesEpsilonComparisonForDirtiness() {
        $model = $this->makeModel();

        $model->floatAttribute = '1.0';
        $model->syncOriginal();

        // Re-assigning an equivalent (but differently formatted) float value
        // should not be considered dirty because of the cast-aware comparison.
        $model->floatAttribute = 1.0;

        $this->assertFalse($model->isDirty('floatAttribute'));

        $model->floatAttribute = 2.0;

        $this->assertTrue($model->isDirty('floatAttribute'));
    }

    public function testArrayCastUsesJsonEquivalenceForDirtiness() {
        $model = $this->makeModel();

        $model->arrayAttribute = ['a' => 1, 'b' => 2];
        $model->syncOriginal();

        $model->arrayAttribute = ['a' => 1, 'b' => 2];

        $this->assertFalse($model->isDirty('arrayAttribute'));

        $model->arrayAttribute = ['a' => 1, 'b' => 3];

        $this->assertTrue($model->isDirty('arrayAttribute'));
    }
}

// @codingStandardsIgnoreStart
class ModelCastingStub extends CModel {
    protected $table = 'model_casting_stubs';

    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'intAttribute' => 'int',
        'floatAttribute' => 'float',
        'doubleAttribute' => 'double',
        'realAttribute' => 'real',
        'stringAttribute' => 'string',
        'boolAttribute' => 'bool',
        'booleanAttribute' => 'boolean',
        'decimalAttribute' => 'decimal:2',
        'arrayAttribute' => 'array',
        'jsonAttribute' => 'json',
        'objectAttribute' => 'object',
        'collectionAttribute' => 'collection',
        'dateAttribute' => 'date',
        'datetimeAttribute' => 'datetime',
        'immutableDateAttribute' => 'immutable_date',
        'immutableDatetimeAttribute' => 'immutable_datetime',
        'customDatetimeAttribute' => 'datetime:Y-m-d',
        'timestampAttribute' => 'timestamp',
    ];
}

class ModelAttributeCastAccessorStub extends CModel {
    protected $table = 'model_attribute_cast_accessor_stubs';

    protected $guarded = [];

    protected function price(): CModel_Casts_Attribute {
        return CModel_Casts_Attribute::make(
            function ($value) {
                return '$' . number_format($value, 2);
            },
            function ($value) {
                return (float) $value;
            }
        );
    }
}

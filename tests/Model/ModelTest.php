<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase {
    /** @var CEvent_DispatcherInterface|null */
    protected $originalDispatcher;

    protected function setUp(): void {
        // The event dispatcher is a single static property shared by every
        // CModel subclass (declared once on CModel via the Event trait), so
        // we must save/restore it to avoid leaking state into other tests.
        $this->originalDispatcher = CModel::getEventDispatcher();
    }

    protected function tearDown(): void {
        m::close();

        if ($this->originalDispatcher) {
            CModel::setEventDispatcher($this->originalDispatcher);
        } else {
            CModel::unsetEventDispatcher();
        }

        // These flags live on CModel itself (shared across all subclasses),
        // so always reset them back to framework defaults.
        CModel::reguard();
        CModel::preventSilentlyDiscardingAttributes(false);

        ModelEventStub::flushEventListeners();
    }

    // -----------------------------------------------------------------
    // Attribute access: magic __get/__set, __isset/__unset, ArrayAccess
    // -----------------------------------------------------------------

    public function testMagicGetAndSetAccessAttributes() {
        $model = new ModelTestStub();

        $model->age = 21;

        $this->assertSame(21, $model->age);
        $this->assertSame(21, $model->getAttribute('age'));
    }

    public function testGetAttributeReturnsNullForUnknownKey() {
        $model = new ModelTestStub();

        $this->assertNull($model->unknownAttribute);
    }

    public function testMagicIssetAndUnset() {
        $model = new ModelTestStub();
        $model->age = 21;

        $this->assertTrue(isset($model->age));

        unset($model->age);

        $this->assertFalse(isset($model->age));
        $this->assertNull($model->age);
    }

    public function testArrayAccessOffsetGetSetExistsUnset() {
        $model = new ModelTestStub();

        $model['age'] = 30;

        $this->assertTrue(isset($model['age']));
        $this->assertSame(30, $model['age']);

        unset($model['age']);

        $this->assertFalse(isset($model['age']));
        $this->assertNull($model['age']);
    }

    // -----------------------------------------------------------------
    // Mass assignment: fill / fillable / guarded / unguard
    // -----------------------------------------------------------------

    public function testFillOnlyFillsFillableAttributes() {
        $model = new ModelTestStub();

        $model->fill(['name' => 'Taylor', 'age' => 30, 'not_fillable' => 'nope']);

        $this->assertSame('Taylor', $model->name);
        $this->assertSame(30, $model->age);
        $this->assertNull($model->not_fillable);
    }

    public function testFillSilentlyIgnoresSpecificallyGuardedAttributeByDefault() {
        $model = new ModelGuardedStub();

        // 'secret' is explicitly guarded (but guarded isn't ['*']), so by
        // default it is silently discarded rather than throwing.
        $model->fill(['visible' => 'ok', 'secret' => 'nope']);

        $this->assertSame('ok', $model->visible);
        $this->assertNull($model->secret);
    }

    public function testFillThrowsMassAssignmentExceptionWhenTotallyGuarded() {
        $model = new ModelTotallyGuardedStub();

        $this->expectException(CModel_Exception_MassAssignmentException::class);

        $model->fill(['name' => 'Taylor']);
    }

    public function testTotallyGuardedReflectsDefaultFillableAndGuardedState() {
        $model = new ModelTotallyGuardedStub();

        $this->assertTrue($model->totallyGuarded());
        $this->assertTrue($model->isGuarded('name'));
        $this->assertFalse($model->isFillable('name'));
    }

    public function testForceFillBypassesGuardedRestrictions() {
        $model = new ModelTotallyGuardedStub();

        $model->forceFill(['name' => 'Taylor']);

        $this->assertSame('Taylor', $model->name);
    }

    public function testUnguardAllowsMassAssignmentOfGuardedAttributes() {
        $model = new ModelTotallyGuardedStub();

        CModel::unguard();

        $this->assertTrue(CModel::isUnguarded());

        $model->fill(['name' => 'Taylor']);

        CModel::reguard();

        $this->assertSame('Taylor', $model->name);
        $this->assertFalse(CModel::isUnguarded());
    }

    public function testUnguardedHelperRunsCallbackUnguardedThenReguards() {
        $model = new ModelTotallyGuardedStub();

        CModel_Trait_GuardsAttributesTestHelper::unguardedFill($model, ['name' => 'Taylor']);

        $this->assertSame('Taylor', $model->name);
        $this->assertFalse(CModel::isUnguarded());
    }

    // -----------------------------------------------------------------
    // Mutators / accessors
    // -----------------------------------------------------------------

    public function testGetMutatorTransformsValueOnRead() {
        $model = new ModelMutatorStub();
        $model->forceFill(['name' => 'taylor']);

        $this->assertSame('TAYLOR', $model->name);
    }

    public function testSetMutatorTransformsValueOnWrite() {
        $model = new ModelMutatorStub();

        $model->slug = 'Hello World';

        $this->assertSame('hello-world', $model->getAttributes()['slug']);
    }

    public function testAttributeBasedAccessorAndMutator() {
        $model = new ModelAttributeCastStub();

        $model->fullName = 'Jane Smith';

        $this->assertSame('Jane', $model->getAttributes()['first_name']);
        $this->assertSame('Smith', $model->getAttributes()['last_name']);
        $this->assertSame('Jane Smith', $model->fullName);
    }

    // -----------------------------------------------------------------
    // Dirty tracking: isDirty / getDirty / getOriginal / syncOriginal / etc
    // -----------------------------------------------------------------

    public function testFreshlyConstructedModelIsDirtyUntilSyncOriginal() {
        // The constructor calls syncOriginal() *before* fill(), so a model
        // hydrated with attributes at construction time starts out dirty
        // (as if every attribute were newly set, e.g. for an unsaved model).
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);

        $this->assertTrue($model->isDirty());
        $this->assertSame([], $model->getOriginal());

        $model->syncOriginal();

        $this->assertFalse($model->isDirty());
        $this->assertTrue($model->isClean());
        $this->assertSame(['name' => 'Taylor', 'age' => 30], $model->getOriginal());
    }

    public function testIsDirtyAndGetDirtyDetectChanges() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 31;

        $this->assertTrue($model->isDirty());
        $this->assertTrue($model->isDirty('age'));
        $this->assertFalse($model->isDirty('name'));
        $this->assertFalse($model->isClean('age'));
        $this->assertTrue($model->isClean('name'));
        $this->assertSame(['age' => 31], $model->getDirty());
    }

    public function testGetOriginalReturnsPreChangeValue() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 99;

        $this->assertSame(30, $model->getOriginal('age'));
        $this->assertSame(99, $model->age);
    }

    public function testSyncOriginalMakesModelClean() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 31;
        $model->syncOriginal();

        $this->assertFalse($model->isDirty());
        $this->assertSame(31, $model->getOriginal('age'));
    }

    public function testGetChangesReflectDirtyStateAfterSyncChanges() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 31;

        $this->assertSame([], $model->getChanges());

        $model->syncChanges();

        $this->assertSame(['age' => 31], $model->getChanges());
    }

    public function testWasChangedReflectsLastSyncedChanges() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 31;
        $model->syncChanges();

        $this->assertTrue($model->wasChanged());
        $this->assertTrue($model->wasChanged('age'));
        $this->assertFalse($model->wasChanged('name'));
    }

    public function testDiscardChangesRevertsAttributesToOriginal() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);
        $model->syncOriginal();

        $model->age = 99;
        $model->discardChanges();

        $this->assertSame(30, $model->age);
        $this->assertFalse($model->isDirty());
    }

    // -----------------------------------------------------------------
    // Serialization: toArray / toJson / jsonSerialize / hidden / visible / appends
    // -----------------------------------------------------------------

    public function testToArrayIncludesPlainAttributes() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);

        $this->assertSame(['name' => 'Taylor', 'age' => 30], $model->toArray());
    }

    public function testToArrayHidesHiddenAttributes() {
        $model = new ModelVisibilityStub();
        $model->forceFill(['name' => 'Taylor', 'password' => 'secret']);

        $array = $model->toArray();

        $this->assertArrayHasKey('name', $array);
        $this->assertArrayNotHasKey('password', $array);
    }

    public function testToArrayOnlyIncludesExplicitlyVisibleAttributes() {
        $model = new ModelExplicitVisibleStub();
        $model->forceFill(['name' => 'Taylor', 'age' => 30, 'secret' => 'nope']);

        $array = $model->toArray();

        $this->assertSame(['name' => 'Taylor'], $array);
    }

    public function testToArrayIncludesAppendedAccessors() {
        $model = new ModelAppendsStub();
        $model->forceFill(['name' => 'Taylor']);

        $array = $model->toArray();

        $this->assertSame('TAYLOR!', $array['shout']);
    }

    public function testToJsonProducesValidJsonOfAttributes() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);

        $json = $model->toJson();

        $this->assertSame(['name' => 'Taylor', 'age' => 30], json_decode($json, true));
    }

    public function testJsonSerializeMatchesToArray() {
        $model = new ModelTestStub(['name' => 'Taylor', 'age' => 30]);

        $this->assertSame($model->toArray(), $model->jsonSerialize());
    }

    // -----------------------------------------------------------------
    // exists / wasRecentlyCreated / is() / isNot()
    // -----------------------------------------------------------------

    public function testExistsAndWasRecentlyCreatedDefaultToFalse() {
        $model = new ModelTestStub();

        $this->assertFalse($model->exists);
        $this->assertFalse($model->wasRecentlyCreated);
    }

    public function testIsComparesKeyTableAndConnection() {
        $one = new ModelIdentityStub(['id' => 1]);
        $one->exists = true;

        $two = new ModelIdentityStub(['id' => 1]);
        $two->exists = true;

        $three = new ModelIdentityStub(['id' => 2]);
        $three->exists = true;

        $this->assertTrue($one->is($two));
        $this->assertTrue($one->isNot($three));
        $this->assertFalse($one->is($three));
    }

    public function testIsReturnsFalseForNull() {
        $model = new ModelIdentityStub(['id' => 1]);

        $this->assertFalse($model->is(null));
        $this->assertTrue($model->isNot(null));
    }

    // -----------------------------------------------------------------
    // key / table
    // -----------------------------------------------------------------

    public function testDefaultPrimaryKeyNameIsId() {
        $model = new ModelPlainStub();

        $this->assertSame('id', $model->getKeyName());
    }

    public function testDefaultTableNameIsSnakeCasePluralOfClassName() {
        $model = new ModelPlainStub();

        $this->assertSame('model_plain_stubs', $model->getTable());
    }

    public function testGetAndSetTable() {
        $model = new ModelPlainStub();

        $model->setTable('custom_table');

        $this->assertSame('custom_table', $model->getTable());
    }

    public function testGetAndSetKeyName() {
        $model = new ModelIdentityStub(['id' => 1]);

        $model->setKeyName('uuid');

        $this->assertSame('uuid', $model->getKeyName());
    }

    public function testGetKeyReturnsPrimaryKeyAttributeValue() {
        $model = new ModelIdentityStub(['id' => 42]);

        $this->assertSame(42, $model->getKey());
    }

    public function testGetRouteKeyDefaultsToPrimaryKey() {
        $model = new ModelIdentityStub(['id' => 42]);

        $this->assertSame('id', $model->getRouteKeyName());
        $this->assertSame(42, $model->getRouteKey());
    }

    public function testGetRouteKeyNameCanBeOverridden() {
        $model = new ModelRouteKeyStub(['id' => 1, 'slug' => 'hello-world']);

        $this->assertSame('slug', $model->getRouteKeyName());
        $this->assertSame('hello-world', $model->getRouteKey());
    }

    // -----------------------------------------------------------------
    // Events
    // -----------------------------------------------------------------

    public function testFireModelEventReturnsTrueWhenNoDispatcherIsRegistered() {
        CModel::unsetEventDispatcher();

        $model = new ModelEventStub();

        $this->assertTrue($model->fireEvent('creating'));
    }

    public function testCreatingListenerCanHaltByReturningFalse() {
        if (!CModel::getEventDispatcher()) {
            CModel::setEventDispatcher(new CEvent_Dispatcher());
        }

        $called = false;

        ModelEventStub::creating(function ($model) use (&$called) {
            $called = true;

            return false;
        });

        $model = new ModelEventStub();
        $result = $model->fireEvent('creating');

        $this->assertTrue($called);
        $this->assertFalse($result);
    }

    public function testObserverMethodsAreCalledForMatchingEvents() {
        if (!CModel::getEventDispatcher()) {
            CModel::setEventDispatcher(new CEvent_Dispatcher());
        }

        ModelEventTestObserver::$called = false;

        ModelEventStub::observe(ModelEventTestObserver::class);

        $model = new ModelEventStub();
        $model->fireEvent('saved', false);

        $this->assertTrue(ModelEventTestObserver::$called);
    }

    public function testWithoutEventsSuppressesRegisteredListeners() {
        if (!CModel::getEventDispatcher()) {
            CModel::setEventDispatcher(new CEvent_Dispatcher());
        }

        $called = false;

        ModelEventStub::creating(function ($model) use (&$called) {
            $called = true;

            return false;
        });

        $model = new ModelEventStub();

        CModel::withoutEvents(function () use ($model) {
            return $model->fireEvent('creating');
        });

        $this->assertFalse($called);
    }
}

// @codingStandardsIgnoreStart
class ModelTestStub extends CModel {
    protected $table = 'model_test_stubs';

    protected $fillable = ['name', 'age'];
}

class ModelGuardedStub extends CModel {
    protected $table = 'model_guarded_stubs';

    protected $guarded = ['secret'];
}

class ModelTotallyGuardedStub extends CModel {
    protected $table = 'model_totally_guarded_stubs';
}

class CModel_Trait_GuardsAttributesTestHelper {
    public static function unguardedFill(CModel $model, array $attributes) {
        return CModel::unguarded(function () use ($model, $attributes) {
            return $model->fill($attributes);
        });
    }
}

class ModelMutatorStub extends CModel {
    protected $table = 'model_mutator_stubs';

    protected $guarded = [];

    public function getNameAttribute($value) {
        return strtoupper($value);
    }

    public function setSlugAttribute($value) {
        $this->attributes['slug'] = strtolower(str_replace(' ', '-', $value));
    }
}

class ModelAttributeCastStub extends CModel {
    protected $table = 'model_attribute_cast_stubs';

    protected $guarded = [];

    protected function fullName(): CModel_Casts_Attribute {
        return CModel_Casts_Attribute::make(
            function ($value, $attributes) {
                return trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? ''));
            },
            function ($value) {
                $parts = explode(' ', $value, 2);

                return [
                    'first_name' => $parts[0],
                    'last_name' => $parts[1] ?? '',
                ];
            }
        );
    }
}

class ModelVisibilityStub extends CModel {
    protected $table = 'model_visibility_stubs';

    protected $guarded = [];

    protected $hidden = ['password'];
}

class ModelExplicitVisibleStub extends CModel {
    protected $table = 'model_explicit_visible_stubs';

    protected $guarded = [];

    protected $visible = ['name'];
}

class ModelAppendsStub extends CModel {
    protected $table = 'model_appends_stubs';

    protected $guarded = [];

    protected $appends = ['shout'];

    public function getShoutAttribute() {
        return strtoupper($this->name) . '!';
    }
}

class ModelIdentityStub extends CModel {
    protected $table = 'model_identity_stubs';

    protected $primaryKey = 'id';

    protected $guarded = [];
}

class ModelPlainStub extends CModel {
    protected $guarded = [];
}

class ModelRouteKeyStub extends CModel {
    protected $table = 'model_route_key_stubs';

    protected $primaryKey = 'id';

    protected $guarded = [];

    public function getRouteKeyName() {
        return 'slug';
    }
}

class ModelEventStub extends CModel {
    protected $table = 'model_event_stubs';

    protected $guarded = [];

    public function fireEvent($event, $halt = true) {
        return $this->fireModelEvent($event, $halt);
    }
}

class ModelEventTestObserver {
    public static $called = false;

    public function saved($model) {
        self::$called = true;
    }
}

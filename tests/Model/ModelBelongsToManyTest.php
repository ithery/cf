<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelBelongsToManyTest extends TestCase {
    protected $builder;

    protected $related;

    protected $parent;

    protected function tearDown() {
        m::close();
    }

    /**
     * @return array
     */
    protected function getRelationArguments($parentKeyValue = 1) {
        $this->parent = m::mock(CModel::class);
        $this->parent->shouldReceive('getAttribute')->with('id')->andReturn($parentKeyValue);
        $this->parent->shouldReceive('getConnectionName')->andReturn(null);
        $this->parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');
        $this->parent->shouldReceive('getUpdatedAtColumn')->andReturn('updated_at');
        $this->parent->shouldReceive('touches')->andReturn(false);
        $this->parent->shouldReceive('freshTimestamp')->andReturn('2021-01-01 00:00:00');

        $this->related = m::mock(CModel::class);
        $this->related->shouldReceive('getTable')->andReturn('roles');
        $this->related->shouldReceive('getKeyName')->andReturn('id');
        $this->related->shouldReceive('getKeyType')->andReturn('int');
        $this->related->shouldReceive('qualifyColumn')->with('id')->andReturn('roles.id');
        $this->related->shouldReceive('touches')->andReturn(false);

        $this->builder = m::mock(CModel_Query::class);
        $this->builder->shouldReceive('getModel')->andReturn($this->related);
        $this->builder->shouldReceive('join')->once()->with('role_user', 'roles.id', '=', 'role_user.role_id');
        $this->builder->shouldReceive('where')->once()->with('role_user.user_id', '=', $parentKeyValue);

        return [$this->builder, $this->parent, 'role_user', 'user_id', 'role_id', 'id', 'id', 'relation_name'];
    }

    /**
     * @return CModel_Relation_BelongsToMany
     */
    protected function getRelation($parentKeyValue = 1) {
        return new CModel_Relation_BelongsToMany(...$this->getRelationArguments($parentKeyValue));
    }

    /**
     * @param array $methods
     *
     * @return CModel_Relation_BelongsToMany|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockRelation(array $methods) {
        return $this->getMockBuilder(CModel_Relation_BelongsToMany::class)
            ->onlyMethods($methods)
            ->setConstructorArgs($this->getRelationArguments())
            ->getMock();
    }

    public function testConstructorSetsAllProperties() {
        $relation = $this->getRelation();

        $this->assertSame($this->builder, $relation->getQuery());
        $this->assertSame($this->parent, $relation->getParent());
        $this->assertSame($this->related, $relation->getRelated());
        $this->assertSame('role_user', $relation->getTable());
        $this->assertSame('user_id', $relation->getForeignPivotKeyName());
        $this->assertSame('role_user.user_id', $relation->getQualifiedForeignPivotKeyName());
        $this->assertSame('role_id', $relation->getRelatedPivotKeyName());
        $this->assertSame('role_user.role_id', $relation->getQualifiedRelatedPivotKeyName());
        $this->assertSame('id', $relation->getParentKeyName());
        $this->assertSame('id', $relation->getRelatedKeyName());
        $this->assertSame('roles.id', $relation->getQualifiedRelatedKeyName());
        $this->assertSame('relation_name', $relation->getRelationName());
        $this->assertSame('pivot', $relation->getPivotAccessor());
        $this->assertSame([], $relation->getPivotColumns());
        $this->assertSame(CModel_Relation_Pivot::class, $relation->getPivotClass());
    }

    public function testQualifiedParentKeyNameIsDelegatedToParent() {
        $relation = $this->getRelation();
        $this->parent->shouldReceive('qualifyColumn')->once()->with('id')->andReturn('users.id');

        $this->assertSame('users.id', $relation->getQualifiedParentKeyName());
    }

    public function testAddConstraintsJoinsAndConstrainsOnParentKey() {
        // The constructor itself performs addConstraints(); the ->once() expectations
        // set in getRelationArguments() are verified automatically by Mockery on tearDown.
        $this->getRelation();

        $this->addToAssertionCount(1);
    }

    public function testAddConstraintsIsSkippedWhenConstraintsAreDisabled() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $related = m::mock(CModel::class);
        $related->shouldReceive('getTable')->andReturn('roles');
        $related->shouldReceive('qualifyColumn')->with('id')->andReturn('roles.id');

        $builder = m::mock(CModel_Query::class);
        $builder->shouldReceive('getModel')->andReturn($related);
        $builder->shouldReceive('join')->once()->with('role_user', 'roles.id', '=', 'role_user.role_id');
        $builder->shouldReceive('where')->never();

        CModel_Relation::noConstraints(function () use ($builder, $parent) {
            new CModel_Relation_BelongsToMany($builder, $parent, 'role_user', 'user_id', 'role_id', 'id', 'id');
        });

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsAreProperlyAddedForIntegerKeys() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('id');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('int');
        $relation->getQuery()->shouldReceive('whereIntegerInRaw')->once()->with('role_user.user_id', [1, 2]);

        $model1 = new ModelBelongsToManyModelStub();
        $model1->setRawAttributes(['id' => 1]);
        $model2 = new ModelBelongsToManyModelStub();
        $model2->setRawAttributes(['id' => 2]);

        $relation->addEagerConstraints([$model1, $model2]);

        $this->addToAssertionCount(1);
    }

    public function testEagerConstraintsFallBackToWhereInForNonIntKeyType() {
        $relation = $this->getRelation();
        $relation->getParent()->shouldReceive('getKeyName')->andReturn('uuid');
        $relation->getParent()->shouldReceive('getKeyType')->andReturn('string');
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('role_user.user_id', ['abc']);

        // Note: a model with the default incrementing=true has its "id" attribute
        // auto-cast to the model's keyType (int) via getCasts(), which would silently
        // turn 'abc' into 0. Use a non-incrementing / string-keyed stub to avoid that.
        $model1 = new ModelBelongsToManyStringKeyModelStub();
        $model1->setRawAttributes(['id' => 'abc']);

        $relation->addEagerConstraints([$model1]);

        $this->addToAssertionCount(1);
    }

    public function testInitRelationSetsEmptyCollectionOnEachModel() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($collection = new CModel_Collection());

        $model = m::mock(CModel::class);
        $model->shouldReceive('setRelation')->once()->with('foo', $collection);

        $models = $relation->initRelation([$model], 'foo');

        $this->assertEquals([$model], $models);
    }

    public function testModelsAreProperlyMatchedToParentsUsingPivotForeignKey() {
        $relation = $this->getRelation();
        $relation->getRelated()->shouldReceive('newCollection')->andReturnUsing(function ($items = []) {
            return new CModel_Collection($items);
        });

        $result1 = new stdClass();
        $result1->pivot = new stdClass();
        $result1->pivot->user_id = 1;

        $result2 = new stdClass();
        $result2->pivot = new stdClass();
        $result2->pivot->user_id = 2;

        $result3 = new stdClass();
        $result3->pivot = new stdClass();
        $result3->pivot->user_id = 2;

        $model1 = new ModelBelongsToManyModelStub();
        $model1->setRawAttributes(['id' => 1]);
        $model2 = new ModelBelongsToManyModelStub();
        $model2->setRawAttributes(['id' => 2]);
        $model3 = new ModelBelongsToManyModelStub();
        $model3->setRawAttributes(['id' => 3]);

        $models = $relation->match(
            [$model1, $model2, $model3],
            new CModel_Collection([$result1, $result2, $result3]),
            'foo'
        );

        $this->assertSame([$result1], $models[0]->foo->all());
        $this->assertSame([$result2, $result3], $models[1]->foo->all());
        $this->assertFalse($models[2]->relationLoaded('foo'));
    }

    public function testGetPivotClassDefaultsToPivot() {
        $relation = $this->getRelation();

        $this->assertSame(CModel_Relation_Pivot::class, $relation->getPivotClass());
    }

    public function testUsingSetsCustomPivotClassAndIsFluent() {
        $relation = $this->getRelation();
        $result = $relation->using(ModelBelongsToManyPivotStub::class);

        $this->assertSame($relation, $result);
        $this->assertSame(ModelBelongsToManyPivotStub::class, $relation->getPivotClass());
    }

    public function testAliasSetsPivotAccessor() {
        $relation = $this->getRelation();
        $result = $relation->alias('assignment');

        $this->assertSame($relation, $result);
        $this->assertSame('assignment', $relation->getPivotAccessor());
    }

    public function testWherePivotAddsQualifiedWhereAndReturnsSelf() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.active', '=', 1, 'and')->andReturn($relation->getQuery());

        $result = $relation->wherePivot('active', '=', 1);

        $this->assertSame($relation, $result);
    }

    public function testOrWherePivotUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.active', '=', 1, 'or')->andReturn($relation->getQuery());

        $relation->orWherePivot('active', '=', 1);

        $this->addToAssertionCount(1);
    }

    public function testWherePivotInDelegatesToWhereIn() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('role_user.active', [1, 2], 'and', false)->andReturn($relation->getQuery());

        $relation->wherePivotIn('active', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testWherePivotNotInNegatesWhereIn() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('role_user.active', [1, 2], 'and', true)->andReturn($relation->getQuery());

        $relation->wherePivotNotIn('active', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotInUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('role_user.active', [1, 2], 'or', false)->andReturn($relation->getQuery());

        $relation->orWherePivotIn('active', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotNotInUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereIn')->once()->with('role_user.active', [1, 2], 'or', true)->andReturn($relation->getQuery());

        $relation->orWherePivotNotIn('active', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testWherePivotNullAddsQualifiedWhereNull() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereNull')->once()->with('role_user.active', 'and', false)->andReturn($relation->getQuery());

        $relation->wherePivotNull('active');

        $this->addToAssertionCount(1);
    }

    public function testWherePivotNotNullNegates() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereNull')->once()->with('role_user.active', 'and', true)->andReturn($relation->getQuery());

        $relation->wherePivotNotNull('active');

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotNullUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereNull')->once()->with('role_user.active', 'or', false)->andReturn($relation->getQuery());

        $relation->orWherePivotNull('active');

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotNotNullUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereNull')->once()->with('role_user.active', 'or', true)->andReturn($relation->getQuery());

        $relation->orWherePivotNotNull('active');

        $this->addToAssertionCount(1);
    }

    public function testWherePivotBetweenDelegatesToWhereBetween() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereBetween')->once()->with('role_user.created_at', [1, 2], 'and', false)->andReturn($relation->getQuery());

        $relation->wherePivotBetween('created_at', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testWherePivotNotBetweenNegates() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereBetween')->once()->with('role_user.created_at', [1, 2], 'and', true)->andReturn($relation->getQuery());

        $relation->wherePivotNotBetween('created_at', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotBetweenUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereBetween')->once()->with('role_user.created_at', [1, 2], 'or', false)->andReturn($relation->getQuery());

        $relation->orWherePivotBetween('created_at', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testOrWherePivotNotBetweenUsesOrBoolean() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('whereBetween')->once()->with('role_user.created_at', [1, 2], 'or', true)->andReturn($relation->getQuery());

        $relation->orWherePivotNotBetween('created_at', [1, 2]);

        $this->addToAssertionCount(1);
    }

    public function testOrderByPivotDelegatesToOrderBy() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('orderBy')->once()->with('role_user.created_at', 'desc')->andReturn($relation->getQuery());

        $relation->orderByPivot('created_at', 'desc');

        $this->addToAssertionCount(1);
    }

    public function testWithPivotValueAddsWherePivotAndStoresDefaultForAttach() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.status', '=', 'active', 'and')->andReturn($relation->getQuery());

        $result = $relation->withPivotValue('status', 'active');

        $this->assertSame($relation, $result);
    }

    public function testWithPivotValueRejectsNullValue() {
        $this->expectException(InvalidArgumentException::class);

        $relation = $this->getRelation();
        $relation->withPivotValue('status', null);
    }

    public function testWithPivotValueAcceptsAnArrayOfColumns() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.status', '=', 'active', 'and')->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.flag', '=', 1, 'and')->andReturn($relation->getQuery());

        $relation->withPivotValue(['status' => 'active', 'flag' => 1]);

        $this->addToAssertionCount(1);
    }

    public function testWithPivotMergesColumnsAndAcceptsVariadicArguments() {
        $relation = $this->getRelation();
        $relation->withPivot(['active', 'created_at']);
        $relation->withPivot('extra');

        $this->assertSame(['active', 'created_at', 'extra'], $relation->getPivotColumns());
    }

    public function testWithTimestampsEnablesTimestampsAndAddsPivotColumns() {
        $relation = $this->getRelation();

        $this->assertFalse($relation->withTimestamps === $relation);
        $result = $relation->withTimestamps();

        $this->assertSame($relation, $result);
        $this->assertTrue($relation->withTimestamps);
        $this->assertSame(['created_at', 'updated_at'], $relation->getPivotColumns());
        $this->assertSame('created_at', $relation->createdAt());
        $this->assertSame('updated_at', $relation->updatedAt());
    }

    public function testWithTimestampsAcceptsCustomColumnNames() {
        $relation = $this->getRelation();
        $relation->withTimestamps('added_at', 'changed_at');

        $this->assertSame(['added_at', 'changed_at'], $relation->getPivotColumns());
        $this->assertSame('added_at', $relation->createdAt());
        $this->assertSame('changed_at', $relation->updatedAt());
    }

    public function testCreatedAtAndUpdatedAtFallBackToParentColumnsByDefault() {
        $relation = $this->getRelation();

        $this->assertSame('created_at', $relation->createdAt());
        $this->assertSame('updated_at', $relation->updatedAt());
    }

    public function testQualifyPivotColumnLeavesAlreadyQualifiedColumnsAlone() {
        $relation = $this->getRelation();

        $this->assertSame('role_user.active', $relation->qualifyPivotColumn('active'));
        $this->assertSame('other_table.active', $relation->qualifyPivotColumn('other_table.active'));
    }

    public function testGetResultsReturnsEmptyCollectionWhenParentKeyIsNull() {
        $relation = $this->getRelation(null);
        $relation->getRelated()->shouldReceive('newCollection')->once()->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->getResults());
    }

    public function testGetResultsCallsGetWhenParentKeyIsPresent() {
        $relation = $this->getRelation();

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->with([
            'roles.*',
            'role_user.user_id as pivot_user_id',
            'role_user.role_id as pivot_role_id',
        ])->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->never();
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([])->andReturn($empty = new CModel_Collection());

        $this->assertSame($empty, $relation->getResults());
    }

    public function testGetHydratesPivotRelationAndEagerLoadsWhenModelsAreFound() {
        $relation = $this->getRelation();

        $queryStub = new stdClass();
        $queryStub->columns = null;
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryStub);
        $relation->getQuery()->shouldReceive('applyScopes')->once()->andReturn($relation->getQuery());
        $relation->getQuery()->shouldReceive('addSelect')->once()->with([
            'roles.*',
            'role_user.user_id as pivot_user_id',
            'role_user.role_id as pivot_role_id',
        ])->andReturn($relation->getQuery());

        $model = new ModelBelongsToManyModelStub();
        $model->setRawAttributes(['id' => 10, 'pivot_user_id' => 1, 'pivot_role_id' => 2]);

        $relation->getQuery()->shouldReceive('getModels')->once()->andReturn([$model]);
        $relation->getQuery()->shouldReceive('eagerLoadRelations')->once()->with([$model])->andReturn([$model]);
        $relation->getRelated()->shouldReceive('newCollection')->once()->with([$model])->andReturn(new CModel_Collection([$model]));

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('setPivotKeys')->once()->with('user_id', 'role_id')->andReturnSelf();
        $relation->getRelated()->shouldReceive('newPivot')->once()
            ->with($relation->getParent(), ['user_id' => 1, 'role_id' => 2], 'role_user', true, null)
            ->andReturn($pivot);

        $results = $relation->get();

        $this->assertCount(1, $results);
        $this->assertSame($pivot, $model->pivot);
        $this->assertFalse(isset($model->pivot_user_id));
        $this->assertFalse(isset($model->pivot_role_id));
    }

    public function testNewPivotMergesDefaultPivotValuesAndSetsKeys() {
        $relation = $this->getRelation();
        $relation->getQuery()->shouldReceive('where')->once()->with('role_user.status', '=', 'active', 'and')->andReturn($relation->getQuery());
        $relation->withPivotValue('status', 'active');

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('setPivotKeys')->once()->with('user_id', 'role_id')->andReturnSelf();
        $relation->getRelated()->shouldReceive('newPivot')->once()
            ->with($relation->getParent(), ['status' => 'active', 'role_id' => 2], 'role_user', false, null)
            ->andReturn($pivot);

        $result = $relation->newPivot(['role_id' => 2]);

        $this->assertSame($pivot, $result);
    }

    public function testNewExistingPivotMarksThePivotAsExisting() {
        $relation = $this->getRelation();

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('setPivotKeys')->once()->with('user_id', 'role_id')->andReturnSelf();
        $relation->getRelated()->shouldReceive('newPivot')->once()
            ->with($relation->getParent(), ['role_id' => 2], 'role_user', true, null)
            ->andReturn($pivot);

        $this->assertSame($pivot, $relation->newExistingPivot(['role_id' => 2]));
    }

    public function testGetCurrentlyAttachedPivotsMapsRawRecordsIntoPivotModels() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('get')->once()->andReturn(new CCollection([
            (object) ['user_id' => 1, 'role_id' => 2],
        ]));

        $method = new ReflectionMethod($relation, 'getCurrentlyAttachedPivots');
        $method->setAccessible(true);
        $pivots = $method->invoke($relation);

        $this->assertCount(1, $pivots);
        $this->assertInstanceOf(CModel_Relation_Pivot::class, $pivots->first());
        $this->assertSame(2, $pivots->first()->role_id);
        $this->assertSame('user_id', $pivots->first()->getForeignKey());
        $this->assertSame('role_id', $pivots->first()->getRelatedKey());
    }

    public function testAttachInsertsFormattedPivotRecord() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $insertQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($insertQuery);
        $insertQuery->shouldReceive('from')->once()->with('role_user')->andReturn($insertQuery);
        $insertQuery->shouldReceive('insert')->once()->with([['role_id' => 2, 'user_id' => 1]])->andReturn(true);

        $relation->attach(2);

        $this->addToAssertionCount(1);
    }

    public function testAttachMergesExtraAttributesIntoPivotRecord() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $insertQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($insertQuery);
        $insertQuery->shouldReceive('from')->once()->with('role_user')->andReturn($insertQuery);
        $insertQuery->shouldReceive('insert')->once()->with([['role_id' => 2, 'user_id' => 1, 'status' => 'active']])->andReturn(true);

        $relation->attach(2, ['status' => 'active']);

        $this->addToAssertionCount(1);
    }

    public function testAttachUsesCustomPivotClassWhenUsingIsSet() {
        $relation = $this->getRelation();
        $relation->using(ModelBelongsToManyPivotStub::class);

        // castAttributes() (invoked while formatting the attach record) builds a throwaway
        // pivot via newPivot() *before* attachUsingCustomClass() builds the real one to
        // save, so related->newPivot() is actually invoked twice here.
        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('setPivotKeys')->with('user_id', 'role_id')->andReturnSelf();
        $pivot->shouldReceive('fill')->with([])->andReturnSelf();
        $pivot->shouldReceive('getAttributes')->andReturn([]);
        $pivot->shouldReceive('save')->once();
        $relation->getRelated()->shouldReceive('newPivot')
            ->with($relation->getParent(), m::type('array'), 'role_user', false, ModelBelongsToManyPivotStub::class)
            ->twice()
            ->andReturn($pivot);

        $relation->attach(2);

        $this->addToAssertionCount(1);
    }

    public function testDetachRemovesOnlyGivenIds() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('whereIn')->once()->with('role_user.role_id', [1, 2, 3])->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('delete')->once()->andReturn(3);

        $this->assertSame(3, $relation->detach([1, 2, 3]));
    }

    public function testDetachWithoutIdsClearsAllPivotRecords() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('whereIn')->never();
        $pivotQuery->shouldReceive('delete')->once()->andReturn(5);

        $this->assertSame(5, $relation->detach());
    }

    public function testDetachWithEmptyArrayBuildsQueryButNeverDeletes() {
        // The pivot query is always built (via newPivotQuery()) before the empty-ids
        // check short-circuits, so getQuery()/newQuery()/from()/where() still run —
        // only whereIn()/delete() are skipped.
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('whereIn')->never();
        $pivotQuery->shouldReceive('delete')->never();

        $this->assertSame(0, $relation->detach([]));
    }

    public function testDetachUsesCustomPivotClassWhenUsingIsSet() {
        $relation = $this->getRelation();
        $relation->using(ModelBelongsToManyPivotStub::class);

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('setPivotKeys')->once()->with('user_id', 'role_id')->andReturnSelf();
        $pivot->shouldReceive('delete')->once()->andReturn(1);
        $relation->getRelated()->shouldReceive('newPivot')->once()
            ->with($relation->getParent(), ['user_id' => 1, 'role_id' => 2], 'role_user', true, ModelBelongsToManyPivotStub::class)
            ->andReturn($pivot);

        $this->assertSame(1, $relation->detach(2));
    }

    public function testUpdateExistingPivotUpdatesMatchingPivotRecord() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('whereIn')->once()->with('role_id', [2])->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('update')->once()->with(['status' => 'active'])->andReturn(1);

        $this->assertSame(1, $relation->updateExistingPivot(2, ['status' => 'active']));
    }

    public function testUpdateExistingPivotUsingCustomClassSavesOnlyWhenDirty() {
        $relation = $this->getMockRelation(['getCurrentlyAttachedPivots']);
        $relation->using(ModelBelongsToManyPivotStub::class);

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('fill')->once()->with(['status' => 'active'])->andReturnSelf();
        $pivot->shouldReceive('isDirty')->once()->andReturn(true);
        $pivot->shouldReceive('save')->once();

        $collection = m::mock(CCollection::class);
        $collection->shouldReceive('where')->once()->with('user_id', 1)->andReturnSelf();
        $collection->shouldReceive('where')->once()->with('role_id', 2)->andReturnSelf();
        $collection->shouldReceive('first')->once()->andReturn($pivot);

        $relation->method('getCurrentlyAttachedPivots')->willReturn($collection);

        $this->assertSame(1, $relation->updateExistingPivot(2, ['status' => 'active']));
    }

    public function testUpdateExistingPivotUsingCustomClassSkipsSaveWhenNotDirty() {
        $relation = $this->getMockRelation(['getCurrentlyAttachedPivots']);
        $relation->using(ModelBelongsToManyPivotStub::class);

        $pivot = m::mock(CModel_Relation_Pivot::class);
        $pivot->shouldReceive('fill')->once()->with([])->andReturnSelf();
        $pivot->shouldReceive('isDirty')->once()->andReturn(false);
        $pivot->shouldReceive('save')->never();

        $collection = m::mock(CCollection::class);
        $collection->shouldReceive('where')->once()->with('user_id', 1)->andReturnSelf();
        $collection->shouldReceive('where')->once()->with('role_id', 2)->andReturnSelf();
        $collection->shouldReceive('first')->once()->andReturn($pivot);

        $relation->method('getCurrentlyAttachedPivots')->willReturn($collection);

        $this->assertSame(0, $relation->updateExistingPivot(2, []));
    }

    public function testSyncDetachesMissingAttachesNewAndSkipsUnchanged() {
        $relation = $this->getMockRelation(['attach', 'detach', 'updateExistingPivot', 'getCurrentlyAttachedPivots', 'touchIfTouching']);

        $collection = m::mock(CCollection::class);
        $collection->shouldReceive('pluck')->once()->with('role_id')->andReturnSelf();
        $collection->shouldReceive('all')->once()->andReturn([1, 2]);
        $relation->method('getCurrentlyAttachedPivots')->willReturn($collection);

        // array_diff() preserves the original array keys of $current (here [0 => 1, 1 => 2]),
        // so the id being detached (2) is passed through under its original index 1, not
        // reindexed to 0. castKeys() then preserves that same key in the returned changes.
        $relation->expects($this->once())->method('detach')->with([1 => 2]);
        $relation->expects($this->once())->method('attach')->with(3, [], false);
        $relation->expects($this->never())->method('updateExistingPivot');
        $relation->expects($this->once())->method('touchIfTouching');

        $changes = $relation->sync([1, 3]);

        $this->assertSame(['attached' => [3], 'detached' => [1 => 2], 'updated' => []], $changes);
    }

    public function testSyncUpdatesExistingRecordsWithNewAttributes() {
        $relation = $this->getMockRelation(['attach', 'detach', 'updateExistingPivot', 'getCurrentlyAttachedPivots', 'touchIfTouching']);

        $collection = m::mock(CCollection::class);
        $collection->shouldReceive('pluck')->once()->with('role_id')->andReturnSelf();
        $collection->shouldReceive('all')->once()->andReturn([1]);
        $relation->method('getCurrentlyAttachedPivots')->willReturn($collection);

        $relation->expects($this->never())->method('detach');
        $relation->expects($this->never())->method('attach');
        $relation->expects($this->once())->method('updateExistingPivot')->with(1, ['status' => 'active'], false)->willReturn(true);
        $relation->expects($this->once())->method('touchIfTouching');

        $changes = $relation->sync([1 => ['status' => 'active']]);

        $this->assertSame(['attached' => [], 'detached' => [], 'updated' => [1]], $changes);
    }

    public function testSyncWithoutDetachingNeverDetaches() {
        $relation = $this->getMockRelation(['attach', 'detach', 'updateExistingPivot', 'getCurrentlyAttachedPivots', 'touchIfTouching']);

        $collection = m::mock(CCollection::class);
        $collection->shouldReceive('pluck')->once()->with('role_id')->andReturnSelf();
        $collection->shouldReceive('all')->once()->andReturn([1]);
        $relation->method('getCurrentlyAttachedPivots')->willReturn($collection);

        $relation->expects($this->never())->method('detach');
        $relation->expects($this->once())->method('attach')->with(2, [], false);
        $relation->expects($this->once())->method('touchIfTouching');

        $changes = $relation->syncWithoutDetaching([1, 2]);

        $this->assertSame(['attached' => [2], 'detached' => [], 'updated' => []], $changes);
    }

    public function testToggleDetachesExistingAndAttachesNew() {
        $pivotQueryMock = m::mock(stdClass::class);
        $pivotQueryMock->shouldReceive('pluck')->once()->with('role_id')->andReturn(new CCollection([1, 2]));

        $relation = $this->getMockBuilder(CModel_Relation_BelongsToMany::class)
            ->onlyMethods(['attach', 'detach', 'touchIfTouching', 'newPivotQuery'])
            ->setConstructorArgs($this->getRelationArguments())
            ->getMock();

        $relation->method('newPivotQuery')->willReturn($pivotQueryMock);

        $relation->expects($this->once())->method('detach')->with([2], false);
        $relation->expects($this->once())->method('attach')->with([3 => []], [], false);
        $relation->expects($this->once())->method('touchIfTouching');

        $changes = $relation->toggle([2, 3]);

        $this->assertSame(['attached' => [3], 'detached' => [2]], $changes);
    }

    public function testAllRelatedIdsPlucksRelatedPivotKey() {
        $relation = $this->getRelation();

        $queryBuilder = m::mock(stdClass::class);
        $pivotQuery = m::mock(stdClass::class);
        $relation->getQuery()->shouldReceive('getQuery')->andReturn($queryBuilder);
        $queryBuilder->shouldReceive('newQuery')->once()->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('from')->once()->with('role_user')->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('where')->once()->with('user_id', 1)->andReturn($pivotQuery);
        $pivotQuery->shouldReceive('pluck')->once()->with('role_id')->andReturn(new CCollection([1, 2, 3]));

        $ids = $relation->allRelatedIds();

        $this->assertSame([1, 2, 3], $ids->all());
    }
}
// @codingStandardsIgnoreStart
class ModelBelongsToManyModelStub extends CModel {
}

class ModelBelongsToManyStringKeyModelStub extends CModel {
    public $incrementing = false;

    protected $keyType = 'string';
}

class ModelBelongsToManyPivotStub extends CModel_Relation_Pivot {
}

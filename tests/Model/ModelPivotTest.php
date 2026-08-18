<?php
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ModelPivotTest extends TestCase {
    protected function tearDown() {
        m::close();
    }

    /**
     * A CModel mock with getConnectionName() stubbed, ready to be handed to
     * fromAttributes()/fromRawAttributes() (both of which call it once).
     *
     * @return \Mockery\MockInterface
     */
    protected function mockParent() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->andReturn(null);
        // fromRawAttributes() re-runs hasTimestampAttributes() *after* pivotParent has
        // already been assigned (unlike the plain fromAttributes() path), so
        // getCreatedAtColumn() ends up delegating to the parent mock at that point.
        $parent->shouldReceive('getCreatedAtColumn')->andReturn('created');

        return $parent;
    }

    public function testFromAttributesSetsConnectionTableAttributesAndSyncsOriginal() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->once()->andReturn('mysql');

        $pivot = CModel_Relation_Pivot::fromAttributes($parent, ['foo' => 'bar'], 'my_pivot_table', true);

        $this->assertSame(['foo' => 'bar'], $pivot->getAttributes());
        $this->assertSame('mysql', $pivot->getConnectionName());
        $this->assertSame('my_pivot_table', $pivot->getTable());
        $this->assertTrue($pivot->exists);
        $this->assertSame($parent, $pivot->pivotParent);
        $this->assertSame([], $pivot->getDirty());
    }

    public function testFromAttributesDefaultsExistsToFalse() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->once()->andReturn(null);

        $pivot = CModel_Relation_Pivot::fromAttributes($parent, ['foo' => 'bar'], 'table');

        $this->assertFalse($pivot->exists);
    }

    public function testFromRawAttributesDoesNotDoubleMutate() {
        // fromRawAttributes() = fromAttributes() (1 getConnectionName call) followed by a
        // second hasTimestampAttributes() check that now *does* consult pivotParent, so
        // getCreatedAtColumn() must be stubbed too (see mockParent()'s docblock).
        $parent = $this->mockParent();

        $pivot = ModelPivotJsonCastStub::fromRawAttributes($parent, ['foo' => json_encode(['name' => 'Taylor'])], 'table', true);

        $this->assertSame(['name' => 'Taylor'], $pivot->foo);
    }

    public function testPropertiesChangedAfterCreationAreDirty() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->once()->andReturn(null);

        $pivot = CModel_Relation_Pivot::fromAttributes($parent, ['foo' => 'bar', 'baz' => 'qux'], 'table', true);
        $pivot->baz = 'changed';

        $this->assertSame(['baz' => 'changed'], $pivot->getDirty());
    }

    public function testTimestampPropertyIsTrueWhenCreatedAtInAttributes() {
        // fromAttributes() calls hasTimestampAttributes() *before* pivotParent is
        // assigned, so getCreatedAtColumn() falls through to the real (non-parent)
        // default here -- CModel::CREATED, i.e. 'created' -- regardless of what the
        // parent mock would say. The parent's getConnectionName() is still needed
        // for setConnection().
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->andReturn(null);

        $withTimestamp = CModel_Relation_Pivot::fromAttributes($parent, ['foo' => 'bar', 'created' => 'now'], 'table');
        $this->assertTrue($withTimestamp->timestamps);

        $withoutTimestamp = CModel_Relation_Pivot::fromAttributes($parent, ['foo' => 'bar'], 'table');
        $this->assertFalse($withoutTimestamp->timestamps);
    }

    public function testTimestampPropertyIsTrueWhenCreatingFromRawAttributes() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getConnectionName')->andReturn(null);
        $parent->shouldReceive('getCreatedAtColumn')->andReturn('created_at');

        $pivot = CModel_Relation_Pivot::fromRawAttributes($parent, ['foo' => 'bar', 'created_at' => 'now'], 'table');

        $this->assertTrue($pivot->timestamps);
    }

    public function testKeysCanBeSetProperlyAndAreFluent() {
        $pivot = new CModel_Relation_Pivot();
        $result = $pivot->setPivotKeys('foreign_id', 'related_id');

        $this->assertSame($pivot, $result);
        $this->assertSame('foreign_id', $pivot->getForeignKey());
        $this->assertSame('related_id', $pivot->getRelatedKey());
        $this->assertSame('related_id', $pivot->getOtherKey());
    }

    public function testPivotModelTableNameIsSingularSnakeCaseOfClassBasenameByDefault() {
        $pivot = new CModel_Relation_Pivot();

        $this->assertSame('pivot', $pivot->getTable());
    }

    public function testGetTableReturnsExplicitlyAssignedTableWithoutGuessing() {
        $pivot = new CModel_Relation_Pivot();
        $pivot->setTable('role_user');

        $this->assertSame('role_user', $pivot->getTable());
    }

    public function testPivotModelWithParentReturnsParentsTimestampColumns() {
        $parent = m::mock(CModel::class);
        $parent->shouldReceive('getCreatedAtColumn')->andReturn('parent_created_at');
        $parent->shouldReceive('getUpdatedAtColumn')->andReturn('parent_updated_at');

        $pivot = new CModel_Relation_Pivot();
        $pivot->pivotParent = $parent;

        $this->assertSame('parent_created_at', $pivot->getCreatedAtColumn());
        $this->assertSame('parent_updated_at', $pivot->getUpdatedAtColumn());
    }

    public function testPivotModelWithoutParentReturnsItsOwnTimestampColumns() {
        // Unlike Laravel, this framework's default CModel::CREATED / CModel::UPDATED
        // constants are 'created' / 'updated', not 'created_at' / 'updated_at'.
        $pivot = new CModel_Relation_Pivot();

        $this->assertSame('created', $pivot->getCreatedAtColumn());
        $this->assertSame('updated', $pivot->getUpdatedAtColumn());
    }

    public function testUnsetRelationsClearsPivotParentAndLoadedRelations() {
        $pivot = new CModel_Relation_Pivot();
        $pivot->pivotParent = 'a-parent';
        $pivot->setRelation('bar', 'baz');

        $this->assertTrue($pivot->relationLoaded('bar'));

        $result = $pivot->unsetRelations();

        $this->assertSame($pivot, $result);
        $this->assertNull($pivot->pivotParent);
        $this->assertFalse($pivot->relationLoaded('bar'));
    }

    public function testGetQueueableIdReturnsPrimaryKeyWhenPresent() {
        $pivot = CModel_Relation_Pivot::fromRawAttributes($this->mockParent(), ['id' => 42], 'table', true);

        $this->assertSame(42, $pivot->getQueueableId());
    }

    public function testGetQueueableIdBuildsCompositeIdentifierWhenPrimaryKeyMissing() {
        $pivot = CModel_Relation_Pivot::fromRawAttributes($this->mockParent(), ['role_id' => 1, 'user_id' => 2], 'table', true);
        $pivot->setPivotKeys('user_id', 'role_id');

        $this->assertSame('user_id:2:role_id:1', $pivot->getQueueableId());
    }

    public function testDeleteWithoutPrimaryKeyDeletesByOriginalForeignAndRelatedKeyValues() {
        $pivot = $this->getMockBuilder(CModel_Relation_Pivot::class)
            ->onlyMethods(['newQueryWithoutRelationships'])
            ->getMock();
        $pivot->setPivotKeys('foreign_id', 'related_id');
        $pivot->foreign_id = 'foreign.value';
        $pivot->related_id = 'related.value';

        $query = m::mock(stdClass::class);
        $query->shouldReceive('where')->once()->with(['foreign_id' => 'foreign.value', 'related_id' => 'related.value'])->andReturn($query);
        $query->shouldReceive('delete')->once()->andReturn(true);
        $pivot->expects($this->once())->method('newQueryWithoutRelationships')->willReturn($query);

        $this->assertTrue($pivot->delete());
        $this->assertFalse($pivot->exists);
    }

    public function testSetKeysForSelectQueryUsesForeignAndRelatedKeysWhenNoPrimaryKeyIsLoaded() {
        $pivot = CModel_Relation_Pivot::fromRawAttributes($this->mockParent(), ['foreign_id' => 1, 'related_id' => 2], 'table', true);
        $pivot->setPivotKeys('foreign_id', 'related_id');

        $query = m::mock(CModel_Query::class);
        $query->shouldReceive('where')->once()->with('foreign_id', 1)->andReturn($query);
        $query->shouldReceive('where')->once()->with('related_id', 2)->andReturn($query);

        $method = new ReflectionMethod($pivot, 'setKeysForSelectQuery');
        $method->setAccessible(true);
        $result = $method->invoke($pivot, $query);

        $this->assertSame($query, $result);
    }

    public function testMorphPivotSetKeysForSelectQueryAddsMorphTypeWhereBeforeKeyWheres() {
        $pivot = CModel_Relation_MorphPivot::fromRawAttributes($this->mockParent(), ['foreign_id' => 1, 'related_id' => 2], 'table', true);
        $pivot->setPivotKeys('foreign_id', 'related_id');
        $pivot->setMorphType('taggable_type');
        $pivot->setMorphClass('App\\Post');

        $query = m::mock(CModel_Query::class);
        $query->shouldReceive('where')->once()->with('taggable_type', 'App\\Post')->andReturn($query);
        $query->shouldReceive('where')->once()->with('foreign_id', 1)->andReturn($query);
        $query->shouldReceive('where')->once()->with('related_id', 2)->andReturn($query);

        $method = new ReflectionMethod($pivot, 'setKeysForSelectQuery');
        $method->setAccessible(true);
        $method->invoke($pivot, $query);

        $this->addToAssertionCount(1);
    }

    public function testMorphPivotDeleteWithoutPrimaryKeyAddsMorphTypeWhere() {
        $pivot = $this->getMockBuilder(CModel_Relation_MorphPivot::class)
            ->onlyMethods(['newQueryWithoutRelationships'])
            ->getMock();
        $pivot->setPivotKeys('foreign_id', 'related_id');
        $pivot->setMorphType('taggable_type');
        $pivot->setMorphClass('App\\Post');
        $pivot->foreign_id = 'foreign.value';
        $pivot->related_id = 'related.value';

        // getDeleteQuery() (inherited from the Pivot's AsPivot trait) first applies the
        // foreign/related key where([...]) before MorphPivot::delete() chains on the
        // extra morph-type where().
        $query = m::mock(stdClass::class);
        $query->shouldReceive('where')->once()->with(['foreign_id' => 'foreign.value', 'related_id' => 'related.value'])->andReturn($query);
        $query->shouldReceive('where')->once()->with('taggable_type', 'App\\Post')->andReturn($query);
        $query->shouldReceive('delete')->once()->andReturn(true);
        $pivot->expects($this->once())->method('newQueryWithoutRelationships')->willReturn($query);

        $result = $pivot->delete();

        // c::tap() returns the query's delete() result unchanged (no int cast here,
        // unlike the parent::delete() branch which does `(int) parent::delete()`).
        $this->assertTrue($result);
    }

    public function testSetMorphTypeAndSetMorphClassAreFluent() {
        $pivot = new CModel_Relation_MorphPivot();

        $result = $pivot->setMorphType('taggable_type');
        $this->assertSame($pivot, $result);

        $result = $pivot->setMorphClass('App\\Post');
        $this->assertSame($pivot, $result);
    }

    public function testMorphPivotGetTableIsSnakeCaseOfClassBasenameByDefault() {
        $pivot = new CModel_Relation_MorphPivot();

        $this->assertSame('morph_pivot', $pivot->getTable());
    }

    public function testMorphPivotGetQueueableIdIncludesMorphFieldsWhenPrimaryKeyMissing() {
        $pivot = CModel_Relation_MorphPivot::fromRawAttributes($this->mockParent(), ['role_id' => 1, 'user_id' => 2], 'table', true);
        $pivot->setPivotKeys('user_id', 'role_id');
        $pivot->setMorphType('taggable_type');
        $pivot->setMorphClass('App\\Post');

        $this->assertSame('user_id:2:role_id:1:taggable_type:App\\Post', $pivot->getQueueableId());
    }
}
// @codingStandardsIgnoreStart
class ModelPivotJsonCastStub extends CModel_Relation_Pivot {
    protected $casts = [
        'foo' => 'json',
    ];
}

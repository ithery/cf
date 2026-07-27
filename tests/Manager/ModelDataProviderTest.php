<?php
use PHPUnit\Framework\TestCase;

/**
 * Integration tests for `CManager_DataProvider_ModelDataProvider`, run
 * against a real sqlite in-memory database (same rationale as
 * tests/Model/NestedSetTest.php - query building needs a real connection,
 * a mocked builder can't catch a broken WHERE/JOIN).
 *
 * This sandbox's default `php` binary has no pdo_sqlite extension, so this
 * file must be run with:
 *   php7.4 vendor/bin/phpcf test --filter=ModelDataProviderTest
 *
 * Covers the class of bug found and fixed on 2026-07-27: `queryCallback`
 * used to be wrapped in raw `Opis\Closure\SerializableClosure`, which hit a
 * PHP reflection edge case and silently serialized to null instead of
 * throwing - breaking team/tenant scoping on every model-backed
 * `SelectSearch` field without any visible error. `queryCallback` is now
 * wrapped in `CFunction_SerializableClosure` (protected against that edge
 * case, see tests/Function/SerializableClosureTest.php); the tests below
 * assert the *effect* survives a real serialize()/unserialize() round trip
 * matching how `CAjax_Engine_SelectSearch` receives it after going through
 * a temp file (CElement_FormInput_SelectSearch::createAjaxUrl()).
 */
class ModelDataProviderTest extends TestCase {
    /** @var CDatabase_Connection_Pdo_SqliteConnection */
    protected $connection;

    protected function setUp(): void {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped(
                'pdo_sqlite is not available for the active PHP CLI - run under a PHP binary that has it '
                . '(e.g. `php7.4 vendor/bin/phpcf test --filter=ModelDataProviderTest`) to actually execute this suite.'
            );
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->connection = new CDatabase_Connection_Pdo_SqliteConnection($pdo, '', '', ['driver' => 'sqlite']);
        $this->connection->setEventDispatcher(new CEvent_Dispatcher());

        ModelDataProviderTest_Team::$conn = $this->connection;
        ModelDataProviderTest_Item::$conn = $this->connection;

        $schema = $this->connection->getSchemaBuilder();

        $schema->create('mdp_teams', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('mdp_team_id');
            $table->string('name');
            $table->unsignedInteger('status')->default(1);
        });

        $schema->create('mdp_items', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('mdp_item_id');
            $table->string('name');
            $table->unsignedInteger('mdp_team_id');
            $table->unsignedInteger('status')->default(1);
        });

        $acme = ModelDataProviderTest_Team::create(['name' => 'Acme']);
        $globex = ModelDataProviderTest_Team::create(['name' => 'Globex']);

        ModelDataProviderTest_Item::create(['name' => 'Widget', 'mdp_team_id' => $acme->mdp_team_id]);
        ModelDataProviderTest_Item::create(['name' => 'Gadget', 'mdp_team_id' => $acme->mdp_team_id]);
        ModelDataProviderTest_Item::create(['name' => 'Gizmo', 'mdp_team_id' => $globex->mdp_team_id]);
    }

    protected function serializeRoundTrip($dataProvider) {
        return unserialize(serialize($dataProvider));
    }

    public function testQueryCallbackSurvivesRealSerializeUnserializeRoundTrip() {
        $teamId = ModelDataProviderTest_Team::where('name', 'Acme')->first()->mdp_team_id;

        $dataProvider = new CManager_DataProvider_ModelDataProvider(
            ModelDataProviderTest_Item::class,
            function (CModel_Query $query) use ($teamId) {
                $query->where('mdp_team_id', $teamId);
            }
        );

        $restored = $this->serializeRoundTrip($dataProvider);

        $names = $restored->getModelQuery()->get()->pluck('name')->sort()->values()->toArray();

        $this->assertSame(['Gadget', 'Widget'], $names);
    }

    public function testQueryCallbackIsNotSilentlyLostAfterRoundTrip() {
        // Direct regression check for the actual failure mode: the old Opis
        // wrapping didn't throw, it just made getModelQuery() forget the
        // scoping closure entirely, so every team's rows leaked through.
        $dataProvider = new CManager_DataProvider_ModelDataProvider(
            ModelDataProviderTest_Item::class,
            function (CModel_Query $query) {
                $query->where('name', 'Widget');
            }
        );

        $restored = $this->serializeRoundTrip($dataProvider);

        $this->assertCount(1, $restored->getModelQuery()->get());
    }

    public function testDotNotationSearchOrAppliesWhereHasOnRelatedTable() {
        $dataProvider = new CManager_DataProvider_ModelDataProvider(ModelDataProviderTest_Item::class);
        $dataProvider->searchOr(['team.name' => 'Globex']);

        $items = $dataProvider->getModelQuery()->get();

        $this->assertSame(['Gizmo'], $items->pluck('name')->toArray());
    }

    public function testPaginateReturnsOnlyRowsMatchingTheQueryCallback() {
        $dataProvider = new CManager_DataProvider_ModelDataProvider(
            ModelDataProviderTest_Item::class,
            function (CModel_Query $query) {
                $query->where('name', 'like', 'G%');
            }
        );

        $restored = $this->serializeRoundTrip($dataProvider);
        $page = $restored->paginate(10, ['*'], 'page', 1);

        $this->assertSame(2, $page->total());
        $names = c::collect($page->items())->pluck('name')->sort()->values()->toArray();
        $this->assertSame(['Gadget', 'Gizmo'], $names);
    }
}

class ModelDataProviderTest_Team extends CModel {
    public static $conn;

    protected $table = 'mdp_teams';

    protected $primaryKey = 'mdp_team_id';

    protected $guarded = [];

    public $timestamps = false;

    public static function resolveConnection($connection = null) {
        return static::$conn;
    }
}

class ModelDataProviderTest_Item extends CModel {
    public static $conn;

    protected $table = 'mdp_items';

    protected $primaryKey = 'mdp_item_id';

    protected $guarded = [];

    public $timestamps = false;

    public static function resolveConnection($connection = null) {
        return static::$conn;
    }

    public function team() {
        return $this->belongsTo(ModelDataProviderTest_Team::class, 'mdp_team_id', 'mdp_team_id');
    }
}

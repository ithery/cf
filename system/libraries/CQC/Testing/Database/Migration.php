<?php

class CQC_Testing_Database_Migration {
    protected $file;

    public function __construct($file) {
        $this->file = $file;
    }

    public function migrate() {
        if (CFile::exists($this->file)) {
            CFile::delete($this->file);
        }
        CFile::put($this->file, '');
        $config = [
            'pdo' => true,
            'type' => 'sqlite',
            'database' => $this->file,
        ];

        CDatabase::manager()->addConnection($config, static::class);
        $connection = c::db(static::class);
        /** @var \CDatabase_Schema_Builder $schemaBuilder */
        $schema = $connection->getSchemaBuilder();
        $this->createSuiteTable($schema);
        $this->createTestTable($schema);
        $this->createQueueTable($schema);
        $this->createRunTable($schema);
    }

    protected function createSuiteTable(CDatabase_Schema_Builder $schema) {
        $schema->create('suite', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('suite_id');

            $table->string('name');

            $table->string('tests_path');

            $table->string('file_mask');

            $table->string('command_options');

            $table->integer('retries')->default(0);
            $table->string('coverage_enabled')->boolean(false);

            $table->string('coverage_index')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    protected function createTestTable(CDatabase_Schema_Builder $schema) {
        $schema->create('test', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('test_id');

            $table->integer('suite_id')->unsigned();

            $table->string('name');
            $table->string('path')->nullable();
            $table->string('state')->default('idle');

            $table->boolean('enabled')->default(true);

            $table->integer('last_run_id')->unsigned()->nullable();

            $table->string('sha1')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        $schema->table('test', function (CDatabase_Schema_Blueprint $table) {
            $table->foreign('suite_id')
                ->references('suite_id')
                ->on('suite')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    protected function createQueueTable(CDatabase_Schema_Builder $schema) {
        $schema->create('queue', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('queue_id');

            $table->integer('test_id')->unsigned();
            $table->tinyInteger('status')->default(1);

            $table->timestamps();
        });
        $schema->table('queue', function (CDatabase_Schema_Blueprint $table) {
            $table->foreign('test_id')
                ->references('test_id')
                ->on('test')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    protected function createRunTable(CDatabase_Schema_Builder $schema) {
        $schema->create('run', function (CDatabase_Schema_Blueprint $table) {
            $table->increments('run_id');

            $table->integer('test_id')->unsigned();

            $table->boolean('was_ok');

            $table->longText('log');

            $table->text('html')->nullable();

            $table->text('png')->nullable();
            $table->timestamp('started_at')->nullable();

            $table->timestamp('ended_at')->nullable();

            $table->timestamp('notified_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
        $schema->table('run', function (CDatabase_Schema_Blueprint $table) {
            $table->foreign('test_id')
                ->references('test_id')
                ->on('test')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
        $schema->table('run', function (CDatabase_Schema_Blueprint $table) {
            $table->index('created');
        });
    }
}

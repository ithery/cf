<?php

use \MongoDB\Client;

class CDatabase_Schema_Manager_MongoDB extends CDatabase_Schema_Manager {
    public function listTables() {
        $database = $this->getMongoDatabase();
        $tables = $database->listCollections();
        return c::collect($tables)->map(function ($item) {
            return $item->getName();
        });
    }

    public function getDatabaseRowCount() {
        $databaseName = $this->db->getDatabaseName();
        $client = $this->getMongoClient();
        $database = $client->selectDatabase($databaseName);
        $cursor = $database->command(['dbstats' => []]);
        $stats = $cursor->toArray()[0];
        return $stats->objects;
    }

    public function getDatabaseSize() {
        $databaseName = $this->db->getDatabaseName();
        $client = $this->getMongoClient();
        $databases = $client->listDatabases();
        $size = 0;
        foreach ($databases as $db) {
            if ($db['name'] == $databaseName) {
                return $db['sizeOnDisk'];
            }
        }
        return $size;
    }

    /**
     * Lists the available databases for this connection.
     *
     * @return array
     */
    public function listDatabases() {
        $client = $this->getMongoClient();
        $databases = $client->listDatabases();

        return c::collect($databases)->map(function ($item) {
            return carr::get($item, 'name');
        });
    }

    protected function _getPortableTableColumnDefinition($tableColumn) {
    }

    /**
     * @return \MongoDB\Client
     */
    public function getMongoClient() {
        return $this->db->driver()->getMongoClient();
    }

    /**
     * @return \MongoDB\Database
     */
    public function getMongoDatabase() {
        return $this->db->driver()->getMongoDatabase();
    }
}

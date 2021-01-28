<?php

class CDatabase_Driver_MongoDB_Result extends CDatabase_Result {
    // Database connection

    /**
     * @var \MongoDB\Client
     */
    protected $mongoClient;

    protected $mongoDatabase;

    // Data fetching types
    protected $fetchType = 'object';

    protected $returnType = stdClass::class;

    protected $cursorArray = [];

    /**
     * Sets up the result variables.
     *
     * @param \MongoDB\Client   $mongoClient   client
     * @param \MongoDB\Database $mongoDatabase database link
     * @param bool              $object        return objects or arrays
     * @param string            $sql           SQL query that was run
     */
    public function __construct(\MongoDB\Client $mongoClient, \MongoDB\Database $mongoDatabase, $object, $sql) {
        $this->mongoClient = $mongoClient;
        $this->mongoDatabase = $mongoDatabase;

        $this->result = $this->getCursor($sql);
        if (!($this->result instanceof \MongoDB\Driver\Cursor)) {
            $this->result = [carr::wrap($this->result)];
            $this->cursorArray = $this->result;
        }

        $this->result($object);

        // Store the SQL
        $this->sql = $sql;
    }

    /**
     * Magic __destruct function, frees the result.
     */
    public function __destruct() {
        if (is_object($this->result)) {
            unset($this->result);
        }
    }

    public function result($object = true, $type = stdClass::class) {
        $this->fetchType = ((bool) $object) ? 'object' : 'array';

        // This check has to be outside the previous statement, because we do not
        // know the state of fetch_type when $object = NULL
        // NOTE - The class set by $type must be defined before fetching the result,
        // autoloading is disabled to save a lot of stupid overhead.
        if ($this->fetchType == 'object') {
            $this->returnType = (is_string($type) and CF::autoLoad($type)) ? $type : 'stdClass';
        } else {
            $this->returnType = $type;
        }

        return $this;
    }

    /**
     * @param null|mixed $object
     * @param mixed      $type
     *
     * @return array
     */
    protected function getCursorArray($object = null, $type = stdClass::class) {
        if ($this->cursorArray == null) {
            if ($object !== null) {
                $this->result($object, $type);
            }

            $this->result->setTypeMap($this->getTypeMap());
            $this->cursorArray = $this->result->toArray();
        }
        return $this->cursorArray;
    }

    public function as_array($object = null, $type = stdClass::class) {
        return $this->result_array($object, $type);
    }

    public function getTypeMap() {
        $options = [];

        if ($this->fetchType == 'array') {
            $options = ['root' => 'array', 'document' => 'array', 'array' => 'array'];
        }
        if ($this->fetchType == 'object' && $this->returnType != stdClass::class) {
            $options = ['root' => $this->returnType, 'document' => $this->returnType];
        }
        return $options;
    }

    public function result_array($object = null, $type = stdClass::class) {
        return json_decode(json_encode($this->getCursorArray($object, $type)), true);
    }

    public function list_fields() {
        $field_names = [];
        while ($field = $this->result->fetch_field()) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    public function seek($offset) {
        if ($this->offsetExists($offset)) {
            // Set the current row to the offset
            $this->current_row = $offset;

            return true;
        }

        return false;
    }

    public function offsetGet($offset) {
        if (!$this->seek($offset)) {
            return false;
        }

        $record = carr::get($this->getCursorArray(), $offset);
        if ($this->fetchType == 'array' && !is_array($record)) {
            $record = json_decode(json_encode($record), true);
        }
        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null) {
        return $this->result_array(false);
    }

    protected function getCursor($sql) {
        $options = [];

        $cursor = null;

        $rawSql = $sql;
        if (is_string($sql)) {
            if (cstr::startsWith($sql, 'db.')) {
                $sql = substr($sql, 3);
                $collectionName = carr::get(explode('.', $sql), 0);
                $sql = substr($sql, strlen($collectionName) + 1);
                $method = carr::get(explode('(', $sql), 0);
                $sql = substr($sql, strlen($method));
                $parameters = [];
                if ($sql != '()') {
                    if (preg_match("/\((.+?)\)/i", $sql, $matches)) {
                        $json = carr::get($matches, 1);
                        $parameters = CHelper::json()->decode($json, true);
                    } else {
                        throw new CDatabase_Exception('Invalid mongo parameter to execute:' . $sql);
                    }
                }

                $cursor = $this->getCollection($collectionName)->$method($parameters, $options);

                if ($cursor == null) {
                    switch ($method) {
                        case'count':
                            $cursor = [[0]];
                    }
                }
            }
        }
        if (is_array($sql)) {
            $cursor = $this->mongoDatabase->command($sql);
        }

        if ($cursor == null) {
            throw new Exception('You have exception on mongo query:' . (is_string($rawSql) ? $rawSql : json_encode($rawSql)));
        }
        return $cursor;
    }

    /**
     * @return \MongoDB\Client
     */
    public function getMongoClient() {
        return $this->mongoClient;
    }

    /**
     * @return \MongoDB\Driver\Manager
     */
    public function getMongoManager() {
        return $this->getMongoClient()->getManager();
    }

    /**
     * @return \MongoDB\Driver\Server
     */
    public function getMongoServer() {
        return $this->getMongoManager()->selectServer($this->getMongoManager()->getReadPreference());
    }

    /**
     * @return \MongoDB\Database
     */
    public function getMongoDatabase() {
        return $this->mongoDatabase;
    }

    /**
     * Get a MongoDB collection.
     *
     * @param string $name
     *
     * @return Collection
     */
    public function getCollection($name) {
        return $this->mongoDatabase->selectCollection($name);
    }

    /**
     * Countable: count
     */
    public function count() {
        return count($this->getCursorArray());
    }

    /**
     * ArrayAccess: offsetExists
     *
     * @param mixed $offset
     */
    public function offsetExists($offset) {
        if ($this->count() > 0) {
            $min = 0;
            $max = $this->count() - 1;

            return !($offset < $min or $offset > $max);
        }

        return false;
    }
}

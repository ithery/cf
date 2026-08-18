<?php

class CDatabase_Analysis_Explainer {
    protected $mysqlVersion;

    protected $rawVersion;

    protected $explainerRows = [];

    protected $hints = [];

    protected $headerRow = [];

    /**
     * @var CDatabase_Connection
     */
    protected $db;

    protected $isMaria;

    public function __construct(CDatabase_Connection $db, $explainResult) {
        $this->db = $db;
        $this->isMaria = false;
        $this->rawVersion = $this->db->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION);

        if (strpos($this->rawVersion, 'Maria') !== false) {
            $this->isMaria = true;
        }

        $this->mysqlVersion = cstr::substr($this->rawVersion, 0, 3);

        $this->mysqlVersion = 5.6;
        $this->headerRow = [];
        $this->explainerRows = [];
        $this->hints = [];
        $this->initExplainCols();
        foreach ($explainResult as $row) {
            $this->explainerRows[] = new CDatabase_Analysis_Explainer_ExplainerRow(
                $row,
                $this->getRowsCount() > 0 ? $this->explainerRows[$this->getRowsCount() - 1] : null,
                $this
            );
        }
    }

    public function addHint($hint) {
        $this->hints[] = $hint;

        return $this;
    }

    public function fetchValue($query) {
        return $this->db->getValue($query);
    }

    public function fetchPairs($query) {
        return $this->db->getList($query);
    }

    public function fetchAll($query) {
        return $this->db->fetchAll($query);
    }

    public function getRowsCount() {
        return count($this->explainerRows);
    }

    /**
     * Explainer::initExplainCols().
     *
     * @return void
     */
    public function initExplainCols() {
        $this->headerRow = [
            'id' => 'The SELECT identifier. This is the sequential number of the SELECT within the query. The value can be NULL if the row refers to the union result of other rows. In this case, the table column shows a value like <unionM,N> to indicate that the row refers to the union of the rows with id values of M and N.',
            'select_type' => 'The type of SELECT',
            'table' => 'The name of the table to which the row of output refers.',
            'type' => 'The join type. For descriptions of the different types, see EXPLAIN Join Types.',
            'possible_keys' => 'The possible_keys column indicates which indexes MySQL can choose from use to find the rows in this table. Note that this column is totally independent of the order of the tables as displayed in the output from EXPLAIN. That means that some of the keys in possible_keys might not be usable in practice with the generated table order.',
            'key' => 'The key column indicates the key (index) that MySQL actually decided to use. If MySQL decides to use one of the possible_keys indexes to look up rows, that index is listed as the key value.',
            'key_len' => 'The key_len column indicates the length of the key that MySQL decided to use. The length is NULL if the key column says NULL. Note that the value of key_len enables you to determine how many parts of a multiple-part key MySQL actually uses.',
            'ref' => 'The ref column shows which columns or constants are compared to the index named in the key column to select rows from the table.',
            'rows' => 'The rows column indicates the number of rows MySQL believes it must examine to execute the query. For InnoDB tables, this number is an estimate, and may not always be exact.',
        ];
        if ((float) $this->mysqlVersion >= 5.7) {
            $this->headerRow['filtered'] = 'The filtered column indicates an estimated percentage of table rows that will be filtered by the table condition. That is, rows shows the estimated number of rows examined and rows × filtered / 100 shows the number of rows that will be joined with previous tables.';
        }

        $this->headerRow['Extra'] = 'This column contains additional information about how MySQL resolves the query. For descriptions of the different values, see EXPLAIN Extra Information.';
    }

    public function getHeaderRow() {
        return $this->headerRow;
    }

    public function getRows() {
        return $this->explainerRows;
    }

    public function getHints() {
        return $this->hints;
    }

    public function getMysqlBaseDocUrl() {
        return 'http://dev.mysql.com/doc/refman/' . $this->mysqlVersion . '/en/explain-output.html';
    }

    public function getHtml() {
        $view = c::view('cresenity.database.explainer-table', [
            'explainer' => $this,
            'mysqlBaseDocUrl' => $this->getMysqlBaseDocUrl()
        ]);

        return $view->render();
    }
}

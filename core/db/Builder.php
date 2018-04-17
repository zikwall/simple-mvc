<?php

namespace core\db;

use PDO;

class Builder extends Connection
{
    /**
     * The primary key for the table.
     *
     * This can (and should) be overridden by the extending class.
     *
     * @access protected
     * @var    string
     */
    protected $_primaryKey = 'id';

    /**
     * Which columns we want to select.
     *
     * To mitigate SQL errors we always append the table name to the start of
     * the field name, whether or not one is supplied. If no table name is
     * passed in then we use the default table the the extended class declared.
     *
     * @access private
     * @var    array
     */
    protected $_select = [];

    /**
     * The tables that we wish to select data from.
     *
     * @access private
     * @var    array
     */
    protected $_from = [];

    /**
     * The clause conditions for where and having to apply to the query.
     *
     * @access private
     * @var    array
     */
    protected $_clause = [];

    /**
     * The having conditions to apply.
     *
     * @access private
     * @var    array
     */
    protected $_having = [];

    /**
     * How our queries should be grouped.
     *
     * @access private
     * @var    array
     */
    protected $_group = [];

    /**
     * How we should order the returned rows.
     *
     * @access private
     * @var    array
     */
    protected $_order = [];

    /**
     * How we should limit the returned rows.
     *
     * @access private
     * @var    array
     */
    protected $_limit = [];

    /**
     * Data that has been passed to the row to insert/update.
     *
     * @access private
     * @var    array
     */
    protected $_store = [];

    /**
     * Data that will be passed to the query.
     *
     * @access private
     * @var    array
     */
    protected $_data;

    /**
     * Whether, after running a query, we should reset the model data.
     *
     * @access private
     * @var    boolean
     */
    protected $_resetAfterQuery = true;

    protected $_table;

    /**
     * @var PDO
     */
    protected $pdo;

    public function __construct()
    {
        parent::connect();
        $this->pdo = $this->getPdoInstance();
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Execute an SQL statement on the database.
     *
     * @access protected
     * @param  string    $sql   The SQL statement to run.
     * @param  array     $data  The data to pass into the prepared statement.
     * @param  boolean   $reset Whether we should reset the model data.
     * @return boolean
     */
    public function run($sql, $data = [], $reset = true)
    {
        if (!$this->_connection) {
            $this->connect();
        }

        $this->_statement = $this->_connection->prepare($sql);
        $result = $this->_statement->execute($data);

        if ($reset) {
            $this->reset();
        }

        return $result;
    }

    /**
     * @param $field
     * @param null $as
     * @return $this
     */
    public function select($field, $as = null) {
        $this->_select[] = ['field' => $field, 'as' => $as];
        return $this;
    }

    /**
     * @param $table
     * @param null $joinType
     * @param null $tableField
     * @param null $joinField
     * @return $this
     */
    public function from($table, $joinType = null, $tableField = null, $joinField = null) {
        $this->_table = $table;
        $this->_from[] = [
            'table'      => $table,
            'joinType'   => $joinType,
            'tableField' => $tableField,
            'joinField'  => $joinField
        ];
        return $this;
    }

    /**
     * Add a where condition to the statement.
     *
     * <code>
     * // 1. A simple condition:
     * ->where('name', '=', 'Zik')
     *
     * // 2. An IN condition:
     * ->where('name', 'IN', array('Zik', 'John', 'Smith'));
     *
     * // You can also use the equals operator for this!
     * ->where('name', '=',  array('Zik', 'John', 'Smith'));
     *
     * // 3. Multiple where's:
     * ->where('name', '=', 'Zik')->where('email', '=', 'zikwall@gmail.com')
     * </code>
     *
     * @access public
     * @param  string       $field    The field we wish to test.
     * @param  string       $operator How we wish to test the field (=, >, etc.)
     * @param  string|array $value    The value to test the field against.
     * @param  string       $joiner   How to join the where clause to the next.
     * @return $this                 For chainability.
     */
    public function where($field, $operator, $value, $joiner = null) {
        $this->_clause[] = [
            'field'    => $field,
            'operator' => $operator,
            'value'    => $value,
            'joiner'   => $joiner
        ];
        return $this;
    }

    /**
     * Add a having condition to the statement.
     *
     * @access public
     * @param  string       $field    The field we wish to test.
     * @param  string       $operator How we wish to test the field (=, >, etc.)
     * @param  string|array $value    The value to test the field against.
     * @param  string       $joiner   How to join the where clause to the next.
     * @param  int          $brace    How many braces to open or close.
     * @return $this                  For chainability.
     */
    public function having($field, $operator, $value, $joiner = null, $brace = 0) {
        $this->_having[] = [
            'field'    => $field,
            'operator' => $operator,
            'value'    => $value,
            'joiner'   => $joiner,
            'brace'    => $brace
        ];
        return $this;
    }

    /**
     * Group by a field.
     *
     * @access public
     * @param  string $field The field that we want to join on.
     * @return $this         For chainability.
     */
    public function group($field)
    {
        $this->_group[] = $field;
        return $this;
    }

    /**
     * @param $status
     * @param null $joiner
     */
    public function brace($status, $joiner = null)
    {
        $this->_clause[] = ($status == 'open' ? '(' : ')')
            . ($joiner ? " {$joiner} " : '');
    }

    /**
     * @param $field
     * @param string $direction
     * @return $this
     */
    public function order($field, $direction = 'ASC')
    {
        $this->_order[] = ['field' => $field, 'direction' => $direction];
        return $this;
    }

    /**
     * @param $limit
     * @param null $start
     * @return $this
     */
    public function limit($limit, $start = null)
    {
        $this->_limit = ['limit' => $limit, 'start' => $start];
        return $this;
    }

    /**
     * @param $type
     * @return string
     */
    public function build($type)
    {
        switch ($type) {
            case 'insert' : $sql = $this->buildInsert(); break;
            case 'select' : $sql = $this->buildSelect(); break;
            case 'update' : $sql = $this->buildUpdate(); break;
            case 'delete' : $sql = $this->buildDelete(); break;
        }
        return $sql;
    }

    /**
     * @return string
     */
    private function buildInsert()
    {
        $keys   = array_keys($this->_store);
        $fields = implode(', ',   $keys);
        $values = implode(', :',  $keys);
        return "INSERT INTO {$this->_table} ({$fields}) VALUES (:{$values})";
    }

    /**
     * @return string
     */
    private function buildSelect()
    {
        return "SELECT {$this->buildFragmentSelect()}
			    FROM   {$this->buildFragmentFrom()}
			           {$this->buildFragmentWhere()}
			           {$this->buildFragmentWhere('HAVING')}
			           {$this->buildFragmentGroup()}
			           {$this->buildFragmentOrder()}
			           {$this->buildFragmentLimit()}";
    }

    /**
     * @return string
     */
    private function buildUpdate()
    {
        return "UPDATE {$this->buildFragmentFrom()}
		        SET    {$this->buildFragmentUpdate()}
		               {$this->buildFragmentWhere()}
		               {$this->buildFragmentLimit()}";
    }

    /**
     * @return string
     */
    private function buildDelete()
    {
        return "DELETE FROM {$this->buildFragmentFrom()}
			                {$this->buildFragmentWhere()}
			                {$this->buildFragmentLimit()}";
    }

    /**
     * @return string
     */
    private function buildFragmentSelect()
    {
        // If there are no fields to select from then just return them all
        if (empty($this->_select)) {
            return '*';
        }
        // Container for the fields we wish to select
        $fields = array();
        // Loop over each field that we want to return and build its SQL
        foreach ($this->_select as $select) {
            $as = $select['as']
                ? " AS '{$select['as']}'"
                : '';
            $fields[] = "{$select['field']} {$as}";
        }
        return implode(', ', $fields);
    }

    /**
     * @return string
     */
    private function buildFragmentFrom()
    {
        // If there are no fields to select from then just return them all
        if (empty($this->_from)) {
            return $this->_table;
        }
        // Container for the tables we wish to use
        $tables = [];
        // Loop over each table and build its SQL
        foreach ($this->_from as $from) {
            $tables[] = $from['tableField'] && $from['joinField']
                ? "{$from['joinType']} JOIN {$from['table']} ON {$from['tableField']} = {$from['joinField']}"
                : $from['table'];
        }
        return implode(', ', $tables);
    }

    /**
     * @return string
     */
    private function buildFragmentUpdate()
    {
        // Container for the fields that will be updated
        $fields = array();
        foreach ($this->_store as $field => $value) {
            // We do not want to update the primary key
            if ($field == $this->_primaryKey) {
                continue;
            }
            $fields[] = "{$field} = :{$field}";
            $this->_data[$field] = $value;
        }
        return implode(', ', $fields);
    }

    /**
     * @param string $type
     * @return string
     */
    private function buildFragmentWhere($type = 'WHERE')
    {
        // If there are no conditions then return nothing
        if ($type == 'HAVING' && empty($this->_having)) {
            return '';
        } else if (empty($this->_clause)) {
            return '';
        }
        // Container for the where conditions
        $sql        = '';
        $sqlClauses = '';
        $clauses    = $type == 'HAVING' ? $this->_having : $this->_clause;
        $clauseType = strtolower($type);
        // Loop over each where condition and build its SQL
        foreach ($clauses as $clauseIndex => $clause) {
            // Are we opening or closing a brace?
            if (! is_array($clause)) {
                $sqlClauses .= $clause;
                continue;
            }
            // The basic perpared variable name
            $clauseVar = "__{$clauseType}_{$clauseIndex}";
            // Reset the SQL for this single clause
            $sql = '';
            // We are dealing with an IN
            if (is_array($clause['value'])) {
                // We need to create the condition as :a, :b, :c
                $clauseIn = array();
                // Loop over each value in the array
                foreach ($clause['value'] as $index => $value) {
                    $clauseIn[] = ":{$clauseVar}_{$index}";
                    $this->_data["{$clauseVar}_{$index}"] = $value;
                }
                // The SQL for this IN
                $sql .= "{$clause['field']} IN (" . implode(', ', $clauseIn) . ")";
            }
            // A simple where condition
            else {
                $sql .= "{$clause['field']} {$clause['operator']} :{$clauseVar}";
                $this->_data[$clauseVar] = $clause['value'];
            }
            // Add any joiner (AND, OR< etc) that the user has added
            $sql .= $clause['joiner'] ? " {$clause['joiner']} " : '';
            // And add to the where clause
            $sqlClauses .= $sql;
        }
        return "{$type} {$sqlClauses}";
    }

    /**
     * @return string
     */
    private function buildFragmentGroup()
    {
        return ! empty($this->_group)
            ? 'GROUP BY ' . implode(', ', $this->_group)
            : '';
    }

    /**
     * @return string
     */
    private function buildFragmentOrder()
    {
        // If there are no order by's then return nothing
        if (empty($this->_order)) {
            return '';
        }
        // Container for the order by's
        $orders = array();
        // Loop over each order by and build its SQL
        foreach ($this->_order as $order) {
            $orders[] = "{$order['field']} {$order['direction']}";
        }
        return 'ORDER BY ' . implode(', ', $orders);
    }

    /**
     * @return string
     */
    private function buildFragmentLimit()
    {
        if (empty($this->_limit)) {
            return '';
        }

        if (! is_null($this->_limit['start'])) {
            return "LIMIT {$this->_limit['start']}, {$this->_limit['limit']}";
        }

        return "LIMIT {$this->_limit['limit']}";
    }

    /**
     * Get how many rows the statement located.
     *
     * @access public
     * @return int|boolean int if statement was successful, boolean false otherwise.
     */
    public function rowCount()
    {
        return $this->_statement
            ? $this->_statement->rowCount()
            : false;
    }


    /**
     * Whether we should reset the query data after we have run the query.
     *
     * @access public
     * @param  boolean $reset
     * @return $this          For chainability.
     */
    public function setReset($reset = true) {
        $this->_resetAfterQuery = $reset;
        return $this;
    }

    /**
     * Reset the query ready for the next one to avoid contamination.
     *
     * Note: This function is called everytime we have run a query automatically.
     *
     * @access public
     */
    public function reset()
    {
        $this->_select = array();
        $this->_from   = array();
        $this->_clause = array();
        $this->_having = array();
        $this->_group  = array();
        $this->_order  = array();
        $this->_limit  = array();
        $this->_data   = array();
        $this->_store  = array();
    }

    /**
     * @param \PDO $method
     * @return bool|mixed
     */
    public function fetch($method = \PDO::FETCH_OBJ)
    {
        return $this->_statement
            ? $this->_statement->fetch($method)
            : false;
    }

    /**
     * @param \PDO $method
     * @return array|bool
     */
    public function fetchAll($method = \PDO::FETCH_OBJ)
    {
        return $this->_statement
            ? $this->_statement->fetchAll($method)
            : false;
    }

    /**
     * Set a variable for the row.
     *
     * Note: This is only used for inserting and updating statements. It will
     * also update any previous value the field had.
     *
     * @access public
     * @param  string $variable The field to manipulate.
     * @param  mixed  $value    The field's value.
     * @magic
     */
    public function __set($variable, $value)
    {
        $this->_store[$variable] = $value;
    }

    /**
     * Get a field value.
     *
     * Note: This is only used for inserting and updating statements. For all
     * other statements you can use the fetch() function.
     *
     * @access public
     * @param  string         $field The name of the field.
     * @return string|boolean        String if exists, boolean false otherwise.
     */
    public function __get($field)
    {
        return isset($this->_store[$field])
            ? $this->_store[$field]
            : false;
    }
    
    /**
     * @return PDO
     */
    public function pdo()
    {
        return $this->pdo;
    }

}
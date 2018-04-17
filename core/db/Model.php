<?php

namespace core\db;

use core\base\Application;


/**
 * Provides a simple interface to gather data from a database.
 *
 * Inserting
 * ---------
 * <code>
 * // 1. Object-orientated inserting:
 * $user = new Model\User();
 * $user->name  = 'Chris';
 * $user->email = 'cjhill@gmail.com';
 * $user->save();
 *
 * // 2. Pass in an array of data:
 * $user = new Model\User();
 * $user->insert(array('name' => 'Chris', 'email' => 'cjhill@gmail.com'));
 * </code>
 *
 * Selecting
 * ---------
 * <code>
 * // 1. Select a single user very quickly:
 * $user = new Model\User(1);
 *
 * // 2. Advanced query selecting:
 * $users = new Model\User();
 * $users->where('active', '=', 1)->where('name', '=', 'Dave')->limit(10)->find();
 *
 * // 3. How many users the query found:
 * echo 'I found ' . $users->rowCount() . ' users.';
 *
 * // 4. Loop over the found users:
 * while ($user = $users->fetch()) {
 *     echo 'Hello, ' . $user->name;
 * }
 * </code>
 *
 * Updating
 * --------
 * <code>
 * 1. Updating a user programatically:
 * $user = new Model\User(1);
 * $user->name = 'Dave';
 * $user->save();
 *
 * 2. Passing in an array of data:
 * $user = new Model\User(1);
 * $user->save(array('name' => 'Dave'));
 *
 * 3. Advanced updating:
 * $user = new Model\User();
 * $user->where('id', '=', array(1, 2))->limit(2)->update(array('name' => 'Dave'));
 * </code>
 *
 * Deleting
 * --------
 * <code>
 * // 1. Simple deletion:
 * $user = new Model\User();
 * $user->delete(1);
 *
 * // 2. Advanced deletion:
 * $user = new Model\User();
 * $user->where('id', '=', 1)->limit(1)->delete();
 * </code>
 *
 * Running your own queries
 * ------------------------
 * <code>
 * // 1. In your User Model, for instance:
 * $this->run('SELECT * FROM user WHERE name = :name', array(':name' => 'Chris'));
 * $user = $this->fetch();
 * echo 'Hello, ' . $user->name;
 * </code>
 *
 */
class Model extends Builder
{
    /**
     * Setup the model.
     *
     * If you want to load a row automatically then you can pass an int to this
     * function, or to load multiple rows then you can pass an array or ints.
     *
     * <code>
     * // Load a single user row
     * $user = new MyProject\Model\User(1);
     * </code>
     *
     * @access public
     * @param  mixed  $id The ID to load automatically.
     */
    public function __construct($id = null) {
        if ($id) {
            $this->where($this->_primaryKey, '=', $id)
                ->limit(1)
                ->find();
            $this->_store = $this->fetch(\PDO::FETCH_ASSOC);
        }

        parent::__construct();
    }

    /**
     * Select some records from a table.
     *
     * @access public
     */
    public function find()
    {
        $this->run($this->build('select'), $this->_data, $this->_resetAfterQuery);
    }

    /**
     * Insert a row into the table.
     *
     * @access public
     * @param  array  $data The data to insert into the table.
     */
    public function insert($data = [])
    {
        // If we have been supplied from data then add it to the store.
        foreach ($data as $field => $value) {
            $this->$field = $value;
        }
        // If the insert was successful then add the primary key to the store
        if ($this->run($this->build('insert'), $this->_store, $this->_resetAfterQuery)) {
            $this->{$this->_primaryKey} = $this->_connection->lastInsertId();
        }
    }

    /**
     * Update a row in the table.
     *
     * @access public
     * @param  array  $data The data to update the table with.
     */
    public function update($data = [])
    {
        // If we have been supplied from data then add it to the store.
        foreach ($data as $field => $value) {
            $this->$field = $value;
        }
        // If the where clause is empty then assume we are updating via primary key
        if (! $this->_clause) {
            $this->where($this->_primaryKey, '=', $this->{$this->_primaryKey});
        }
        // If the insert was successful then add the primary key to the store
        $this->run($this->build('update'), $this->_data, $this->_resetAfterQuery);
    }

    /**
     * Shorthand for the insert and update functions.
     *
     * @access public
     * @param  array  $data The data to insert or update.
     */
    public function save($data = [])
    {
        $this->{$this->_primaryKey}
            ? $this->update($data)
            : $this->insert($data);
    }

    /**
     * Delete rows from the table.
     *
     * @access public
     * @param  int    $id The ID of the row we wish to delete.
     */
    public function delete($id = null)
    {
        // Is there an ID that we need to delete?
        if ($id) {
            $this->where($this->_primaryKey, '=', $id);
        }
        $this->run($this->build('delete'), $this->_data, $this->_resetAfterQuery);
    }

}
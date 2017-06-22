<?php

class MyPDO extends PDO {

    public function __construct($dsn, $username, $password, $options = []) {
        parent::__construct($dsn, $username, $password, $options);
        //$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, array('MyPDOStatement', array('PDO', array($this))));
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * @param string $statement
     * @param array $driver_options
     * @return MyPDOStatement|PDOStatement
     */
    public function prepare ($statement, $driver_options = array()) {
        return parent::prepare($statement, $driver_options);
    }
}
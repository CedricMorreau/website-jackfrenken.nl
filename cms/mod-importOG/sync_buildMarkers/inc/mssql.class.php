<?php

/**
 * Database abstraction layer (MSSQL version), instantiated through.
 * 
 * @name mssql
 * @author Marvin Janssen
 */
class mssql {

    protected $connection = false; // database resource ID

    /**
     * Constructor, connect to the database 
     * 
     * @name mssql()
     * @param string $host
     * @return instance
     * @author Marvin Janssen
     */

    public function mssql($host, $name, $username, $password) {

        // Double checks... since sqlsrv_connect ONLY works on windows machines
        if (function_exists('mssql_connect')) {

            $server = $host;

            // Connect
            $this->connection = mssql_connect($server, $username, $password) or die("Error");

            // Select database...
            mssql_select_db($name, $this->connection);
        } else {
            $connection_info = array(
                "Database" => $name,
                "UID" => $username,
                "PWD" => $password
            );
            $this->connection = sqlsrv_connect($host, $connection_info);
            if (!$this->connection) {
                $error = sqlsrv_errors();
                die("[DATABASE] Could not connect to the database: SQLSTATE: " . $error[0]["SQLSTATE"] . " CODE: " . $error[0]["code"] . " MESSAGE: " . $error[0]["message"]);
            }
        }
    }

    /**
     * Is the database connected? 
     * 
     * @name connected()
     * @param void
     * @return bool
     * @author Marvin Janssen
     */
    public function connected() {
        return ($this->connection !== false);
    }

    /**
     * Query the database.
     * 
     * @name query()
     * @param string $query
     * @return mixed
     * @author Marvin Janssen
     */
    public function query($query) {
        // sqlsrv_query needs the option "Scrollable" => SQLSRV_CURSOR_KEYSET
        // to make sqlsrv_num_rows() work.
        if ($this->connected()) {
            // Double checks... since sqlsrv_connect ONLY works on windows machines
            if (function_exists('mssql_connect')) {

                $result = mssql_query($query, $this->connection);

                return $result;
            } else {

                $result = sqlsrv_query($this->connection, $query, array(), array("Scrollable" => SQLSRV_CURSOR_KEYSET));

                //echo '<pre>';
                //print_r($this->fetch_assoc($result));
                //echo '</pre>';
                if ($result === false) {
                    $error = sqlsrv_errors();
                    die("[DATABASE] Query error: SQLSTATE: " . $error[0]["SQLSTATE"] . " CODE: " . $error[0]["code"] . " MESSAGE: " . $error[0]["message"] . " QUERY: " . $query);
                }
                return $result;
            }
        }
        return false;
    }

    /**
     * Fetch a result as an associative array.
     * 
     * @name escape()
     * @param resource $res
     * @return array
     * @author Marvin Janssen
     */
    public function fetch_assoc($result) {
        // Double checks... since sqlsrv_connect ONLY works on windows machines
        if (function_exists('mssql_connect')) {
            return mssql_fetch_array($result, MSSQL_ASSOC);
        } else {
            return sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
        }
    }

    /**
     * Retrieves the number of rows in a result set.
     * 
     * @name row_count()
     * @param resource $res
     * @return integer
     * @author Marvin Janssen
     */
    public function row_count($result) {
        if (function_exists('mssql_connect')) {
            return mssql_num_rows($result);
        } else {
            return sqlsrv_num_rows($result);
        }
    }

    /**
     * Escape a string for use as query parts. 
     * 
     * @name escape()
     * @param string $string
     * @return string
     * @author Marvin Janssen
     */
    public function escape($string) {
        $string = str_replace("\0", "[NULL]", $string);
        return str_replace("'", "''", $string);
        // if(is_numeric($data))
        // return $data;
        // $unpacked = unpack('H*hex', $data);
        // return '0x' . $unpacked['hex'];
    }

}

?>
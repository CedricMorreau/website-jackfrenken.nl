<?php

class Database {

    // This class handles the database

    private $database;
    private $link;
    private $queryCount = 0;
    private $queries = array();
    private $debug = false;
    private $previous_charset;
    
    public function connection($user, $password, $host) {
    
        // Attempts to connect to the database
        $this->link = new mysqli($host, $user, $password);

        // Any error?
        // Below would not work in 5.2.9 & 5.3.0, so only execute if we do not have those versions
        if (!Core::comparePHPVer('5.2.9') && !Core::comparePHPVer('5.3.0')) {
            
            if ($this->link->connect_error) {
            
                throw new Exception('Connect Error ('.$this->link->connect_errno.'): '.$this->link->connect_error);
            
            }
        
        }
        // Else use a fallback function
        else {
            
            if (mysqli_connect_error()) {
                
                throw new Exception('Connect Error ('.mysqli_connect_errno().'): '.mysqli_connect_error());
                
            }
            
        }
        
        $this->debug = true;
    
    }

    public function set_charset($charset) {
        $this->previous_charset = $this->link->character_set_name();
        $this->link->set_charset($charset);
    }

    public function restore_charset() {
        $this->link->set_charset($this->previous_charset);
    }
    
    /**
     * Since we use MySQLi now, it'd be nice if we actually use prepare properly!
     * @param sql String The actual query!
     * @param typeDef String The values to replace (s = string, d = double, i = integer, b = blob)
     * @param params Array The parameters in an array
     */
    public function prepare($sql, $typeDef = FALSE, $params = FALSE) {
        
        $keyQuery = count($this->queries);
        
        // If debugging is on, spit out the query
        if ($this->debug) {
            
            if (!$typeDef || !$params) {
                
                $this->queries[$keyQuery]['query'] = $sql;
            }
            else {
                
                if (isset($params[0]) && !is_array($params[0])) {
                
                    $qm = explode(',', $this->generateQuestionMarks(count($params)));

                    // Escape EXISTING %
                    $newSQL = str_replace('%', '%%', $sql);
                    // Replace the question marks for debugging purposes
                    $newSQL = str_replace('?', '%s', $newSQL);
                    
                    $this->queries[$keyQuery]['query'] = vsprintf($newSQL, $params);
                }
                else {
                    
                    foreach ($params as $key => $val) {
                        
                        $qm = explode(',', $this->generateQuestionMarks(count($val)));

                        // Replace the question marks for debugging purposes
                        $newSQL = str_replace('?', '%s', $sql);
                        $this->queries[$keyQuery]['prepared_query'][] = vsprintf($newSQL, $val);
                    }
                }
            }
        }
        
        $link = $this->link;
        
        $time_start = microtime(true);
        
        if($stmt = mysqli_prepare($link,$sql)){ 
//             if(count($params) == count($params,1)){ 
              $params = array($params); 
              $multiQuery = FALSE; 
//             } else { 
//               $multiQuery = TRUE; 
//             }  
            
            if($typeDef){ 
              $bindParams = array();    
              $bindParamsReferences = array(); 
              $bindParams = array_pad($bindParams,(count($params,1)-count($params))/count($params),"");         
              foreach($bindParams as $key => $value){ 
                $bindParamsReferences[$key] = &$bindParams[$key];  
              } 
              array_unshift($bindParamsReferences,$typeDef); 
              $bindParamsMethod = new ReflectionMethod('mysqli_stmt', 'bind_param'); 
              $bindParamsMethod->invokeArgs($stmt,$bindParamsReferences); 
            }
            
            $result = array();
            foreach($params as $queryKey => $query){
              if (isset($bindParams) && count($bindParams) > 0) {
              foreach($bindParams as $paramKey => $value){
                $bindParams[$paramKey] = $query[$paramKey];
              }
              }
              $queryResult = array(); 
              if(mysqli_stmt_execute($stmt)){ 
                $resultMetaData = mysqli_stmt_result_metadata($stmt); 
                if($resultMetaData){                                                                               
                  $stmtRow = array();   
                  $rowReferences = array(); 
                  while ($field = mysqli_fetch_field($resultMetaData)) { 
                    $rowReferences[] = &$stmtRow[$field->name]; 
                  }                                
                  mysqli_stmt_store_result($stmt);
                  mysqli_free_result($resultMetaData); 
                  $bindResultMethod = new ReflectionMethod('mysqli_stmt', 'bind_result'); 
                  $bindResultMethod->invokeArgs($stmt, $rowReferences);
                  while(mysqli_stmt_fetch($stmt)){ 
                    $row = array(); 
                    foreach($stmtRow as $key => $value){ 
                      $row[$key] = $value;           
                    } 
                    $queryResult[] = $row; 
                  } 
                  mysqli_stmt_free_result($stmt); 
                } else { 
                  $queryResult[] = mysqli_stmt_affected_rows($stmt); 
                } 
              } else { 
                $queryResult[] = FALSE; 
              } 
              $result[$queryKey] = $queryResult; 
            } 
            mysqli_stmt_close($stmt);   
          } else { 
            $result = FALSE; 
          } 
          
          $time_end = microtime(true);
          
          $this->queries[$keyQuery]['execution_time'] = ($time_end - $time_start);

          echo mysqli_error($link);
          
          if($multiQuery){ 
            return $result; 
          } else { 
            return $result[0]; 
          } 
        
    }
    
    /**
     * In order to connect properly, we're supposed to select a database.
     * @param newDatabase {String} Name of the database we're selecting
     */
    public function selectDatabase($newDatabase) {
            
        // Select a database
        $this->link->select_db($newDatabase) or die ("Error: Selected database not found.");
        $this->database = $newDatabase;
    
        // Select a database
        //mysql_select_db($newDatabase) or die ("Error: Selected database not found.");
        //$this->database = $newDatabase;
    
    }
    
    /**
     * This function returns the $link required for mysqli
     */
    public function getLink() {
        
        return $this->link;
        
    }
    
    /**
     * This function is called to execute a query. Not only does it execute it, it also saves it temporarily in order to see what queries were executed on a page (see debug function "fetchQueries")
     * @param query {String} The actual query as you would specify in a normal SQL query
     */
    public function execQuery($query) {
    
        // Execute a query and add to count
    
        $this->queryCount++;
        $this->queries[] = $query;
        $query = $this->link->query($query) or die('MySQLi Error: '.mysqli_error($this->link));
        return $query;
    
    }
    
    /**
     * This function is called to execute a query. Not only does it execute it, it also saves it temporarily in order to see what queries were executed on a page (see debug function "fetchQueries")
     * @param query {String} The actual query as you would specify in a normal SQL query
     */
    public function fetch($query) {
    
        // Execute a query and add to count
    
        // Query already executed once?
        if (is_object($query))
            $result = $query->fetch_array(MYSQLI_BOTH);
        else
            $result = $this->execQuery($query)->fetch_array(MYSQLI_BOTH);
        
        return $result;
    
    }
    
    /**
     * This function is called to count the rows
     * @param query {String} The actual query as you would specify in a normal SQL query
     */
    public function num($query) {
    
        // Execute a query and add to count
    
        // Query already executed once?
        if (is_object($query))
            $result = $query->num_rows;
        else
            $result = $this->execQuery($query)->num_rows;
        
        return $result;
    
    }
    
    /**
     * This function escapes
     * @param query {String} The actual query as you would specify in a normal SQL query
     */
    public function escape($query) {
    
        $result = $this->link->real_escape_string($query);
        
        return $result;
    
    }
    
    /**
     * Debug function; returns all queries executes on the current page in the form of an array
     */
    public function fetchQueries() {
    
        if ($this->debug)
            return $this->queries;
    }
    
    /**
     * Function which returns the last inserted ID
     */
    public function lastId() {
        
        return $this->link->insert_id;
        
    }
    
    /**
     * This function simply loops through an array, escaping everything
     * @param array {Array} Array which contains variables which require escaping
     */
    public function escapeArray($array) {
        
        $returnArray = array();
        foreach ($array as $key => $val) {
            
            if (is_array($val)) {

                $returnArray[$key] = $this->escapeArray($val);
            }
            else {

                // If a field contains "nenc" at the end, we need to ignore encoding it.
                $explodeKey = explode("_", $key);
                
                // If nenc is not in array
                if (!in_array("nenc", $explodeKey)) {
                        
                    // Simple HTML encode the entire string...
                    $returnArray[$key] = htmlentities($val);
                
                }
                
                // For safety, if html encode did not fix it, replace the apostrophes with a proper HTML encoded one.
                $returnArray[$key] = str_replace("'", "&#39;", $returnArray[$key]);
                
                // If that somehow did not work, still escape the array using proper PHP escaping
                $returnArray[$key] = $this->escape($returnArray[$key]);

            }
            
        }
        
        return $returnArray;
        
    }
    
    /**
     * This function replaces the encoded apostrophes in a variables with a backslashes version (javascript compatibility)
     * @param var {String} Variable to replace
     */
    public function escapeEncoded($var) {
        
        $var = $this->escape($var);
        $var = str_replace("&#39;", "\\&#39;", $var);
        
        return $var;
        
    }
    
    /**
     * Debug function; possible to be used in order to identify what database we're currently connected to.
     */
    public function getDatabase() {
    
        // Show the currently selected database
        if (empty($this->database)) {
            return 'No database selected.';
        }
        else
            return $this->database;
    
    }
    
    public function generateQuestionMarks($aantal) {
        
        return implode(',', array_fill(0, $aantal, '?'));
    }

    public function generateFieldtypes($veldtype, $aantal) {
        
        return str_repeat($veldtype, $aantal);
    }

}

?>
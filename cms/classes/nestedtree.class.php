<?php

class NestedTree {
    
    private $table;
    private $prefix;
    private $db;
    private $count;
    private $tree;
    private $sortBy;
    
    /**
     * Constructor; initiates the nested tree class
     * @param string $table The table name to use
     * @param string $prefix The table prefix to use
     * @param object $db The active database connection object
     */
    public function __construct($table, $prefix, $db, $sortBy = '') {
        
        $this->table = $table;
        $this->prefix = $prefix;
        $this->db = $db;
        $this->sortBy = $sortBy;
        
        // Count how many cats in the tree right now
        $this->count();
        
        if ($this->count > 0) {
            
            $this->selectTree();
        }
    }
    
    public function addNode($leftNode, $nodeName) {
        
        // First off... does the left node even exist?
        if ($this->nodeExists($leftNode)) {
        
            if ($this->count == 0) {

                // There are NO nodes yet, so this will be the first.. with left 1 and right 2
                $this->db->prepare("INSERT INTO `" . $this->table . "` (`" . $this->prefix . "dateAdded`, `" . $this->prefix . "dateAddedBy`, `" . $this->prefix . "dateEdited`, `" . $this->prefix . "dateEditedBy`, `" . $this->prefix . "root`, `" . $this->prefix . "left`, `" . $this->prefix . "right`, `" . $this->prefix . "name`) VALUES(NOW(), ?, NOW(), ?, 1, 1, 2, ?)", "iis", array(CMS::g('common', 'cms_us_id'), CMS::g('common', 'cms_us_id'), $nodeName));
            }
            else {

                $nodeCount = $this->nodeCount($leftNode);

                // Run multiple queries to add it, including a LOCK TABLE to prevent double writing
                $this->db->execQuery("LOCK TABLE `" . $this->table . "` WRITE");

                // Select @myLeft
                $fetchLeft = $this->db->prepare("SELECT `" . $this->prefix . "left` as `myLeft` FROM `" . $this->table . "` WHERE `" . $this->prefix . "id`=?", "i", array($leftNode));

                // Update right and left for nodes
                $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right`=`" . $this->prefix . "right` + 2 WHERE `" . $this->prefix . "right` > " . $fetchLeft[0]['myLeft']);
                $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "left`=`" . $this->prefix . "left` + 2 WHERE `" . $this->prefix . "left` > " . $fetchLeft[0]['myLeft']);

                // Insert the new node
                $this->db->prepare("INSERT INTO `" . $this->table . "` (`" . $this->prefix . "dateAdded`, `" . $this->prefix . "dateAddedBy`, `" . $this->prefix . "dateEdited`, `" . $this->prefix . "dateEditedBy`, `" . $this->prefix . "root`, `" . $this->prefix . "left`, `" . $this->prefix . "right`, `" . $this->prefix . "name`) VALUES(NOW(), ?, NOW(), ?, 0, " . $fetchLeft[0]['myLeft'] . " + 1, " . $fetchLeft[0]['myLeft'] . " + 2, ?)", "iis", array(CMS::g('common', 'cms_us_id'), CMS::g('common', 'cms_us_id'), $nodeName));

                // Unlock tables
                $this->db->execQuery("UNLOCK TABLES");
            }

            // Tree had an update, select and build a new tree
            $this->selectTree();
        
        }
    }
    
    public function deleteNode($nodeId) {
        
        // Check if node even exists
        if ($this->nodeExists($nodeId)) {
            
            // Start deletion procedure
            // Run multiple queries to delete it, including a LOCK TABLE to prevent double writing
            $this->db->execQuery("LOCK TABLE `" . $this->table . "` WRITE");
            
            // Select the left and right to delete
            $fetchData = $this->db->prepare("SELECT `" . $this->prefix . "left` as `myLeft`, `" . $this->prefix . "right` as `myRight`, (`" . $this->prefix . "right` - `" . $this->prefix . "left` + 1) as `myWidth` FROM `" . $this->table . "` WHERE `" . $this->prefix . "id`=?", "i", array($nodeId));
            
            // Delete the entire node
            $this->db->prepare("DELETE FROM `" . $this->table . "` WHERE `" . $this->prefix . "left` BETWEEN ? AND ?", "ii", array($fetchData[0]['myLeft'], $fetchData[0]['myRight']));
            
            // Update remaining nodes
            $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right` = `" . $this->prefix . "right` - ? WHERE `" . $this->prefix . "right` > ?", "ii", array($fetchData[0]['myWidth'], $fetchData[0]['myRight']));
            $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "left` = `" . $this->prefix . "left` - ? WHERE `" . $this->prefix . "left` > ?", "ii", array($fetchData[0]['myWidth'], $fetchData[0]['myRight']));
            
            // Unlock tables
            $this->db->execQuery("UNLOCK TABLES");
            
            // Tree had an update, select and build a new tree
            $this->selectTree();
        }
    }
    
    public function getLastNodeId() {
        
        $lastNode = $this->db->prepare("SELECT `" . $this->prefix . "id` as `id` FROM `" . $this->table . "` ORDER BY `" . $this->prefix . "id` DESC LIMIT 1");
        
        return $lastNode[0]['id'];
    }
    
    public function moveNode($nodeId, $moveToNodeId) {
        
        // Check if nodes exist
        if ($this->nodeExists($nodeId) && $this->nodeExists($moveToNodeId) && $nodeId != $moveToNodeId) {
            
            // Start move procedure
            
            // Fetch left, right and width of moving node
            $fetchMove = $this->subTree($nodeId);
            
            // Run multiple queries to move it, including a LOCK TABLE to prevent double writing
            $this->db->execQuery("LOCK TABLE `" . $this->table . "` WRITE");
            
            // Fetch left, right and width of moveToNode
            $fetchMoveTo = $this->db->prepare("SELECT `" . $this->prefix . "left` as `myLeft`, `" . $this->prefix . "right` as `myRight`, (`" . $this->prefix . "right` - `" . $this->prefix . "left` + 1) as `myWidth` FROM `" . $this->table . "` WHERE `" . $this->prefix . "id`=?", "i", array($moveToNodeId));
            
            if ($fetchMove[0]['myLeft'] > $fetchMoveTo[0]['myLeft']) {
                
                // Set all myLeft / myRight BIGGER than $fetchMoveTo[0]['myLeft'] to + myWidth
                $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right` = `" . $this->prefix . "right` + ? WHERE `" . $this->prefix . "right` > ? AND `" . $this->prefix . "right` <= ?", "iii", array($fetchMove[0]['myWidth'], $fetchMoveTo[0]['myLeft'], $fetchMove[0]['myRight']));
            $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "left` = `" . $this->prefix . "left` + ? WHERE `" . $this->prefix . "left` > ? AND `" . $this->prefix . "left` <= ?", "iii", array($fetchMove[0]['myWidth'], $fetchMoveTo[0]['myLeft'], $fetchMove[0]['myRight']));
            
                // Loop through $fetchMove to handle them individually (-(myWidth - 1)!)
                foreach ($fetchMove as $key => $val) {
                    
                    $newWidth = ($fetchMove[0]['myLeft'] + $fetchMove[0]['myWidth']) - ($fetchMoveTo[0]['myLeft'] + 1);
                    
                    $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right`=`" . $this->prefix . "right` - ?, `" . $this->prefix . "left`=`" . $this->prefix . "left` - ? WHERE `" . $this->prefix . "id`=?", "iii", array($newWidth, $newWidth, $val['id']));
                }
            }
            else {
                
                // Set all myLeft / myRight SMALLER than $fetchMoveTo[0]['myLeft'] to + myWidth
                $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right` = `" . $this->prefix . "right` - ? WHERE `" . $this->prefix . "right` >= ? AND `" . $this->prefix . "right` < ?", "iii", array($fetchMove[0]['myWidth'], $fetchMove[0]['myLeft'], $fetchMoveTo[0]['myRight']));
            $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "left` = `" . $this->prefix . "left` - ? WHERE `" . $this->prefix . "left` >= ? AND `" . $this->prefix . "left` < ?", "iii", array($fetchMove[0]['myWidth'], $fetchMove[0]['myLeft'], $fetchMoveTo[0]['myRight']));
            
                // Loop through $fetchMove to handle them individually (-(myWidth - 1)!)
                foreach ($fetchMove as $key => $val) {
                    
                    $newWidth = $fetchMoveTo[0]['myRight'] - $fetchMove[0]['myWidth'];
                    $newWidth = $newWidth - ($fetchMove[0]['myLeft'] - $fetchMove[0]['myWidth']);
                    
                    $this->db->prepare("UPDATE `" . $this->table . "` SET `" . $this->prefix . "right`=`" . $this->prefix . "right` + ?, `" . $this->prefix . "left`=`" . $this->prefix . "left` + ? WHERE `" . $this->prefix . "id`=?", "iii", array($newWidth, $newWidth, $val['id']));
                }
            }
            
            // Now we update all nodes that are BIGGER than moveTo left and smaller or equal than 
            
            // Unlock tables
            $this->db->execQuery("UNLOCK TABLES");
            
            // Tree had an update, select and build a new tree
            $this->selectTree();
        }
    }
    
    public function returnCleanTree() {
        
        return $this->tree;
    }
    
    /**
     * Returns the entire tree, alphabetically sorted in a nested array
     * @return array The tree
     */
    public function returnTree() {
        
        $tree = $this->tree;
        
        $out = array();
        $depth = 0;
        $depthParents[0] = &$out;
        
        foreach ($tree as $key => $val) {
            
            if (!empty($this->sortBy))
                $sortOrder = sprintf("%010s", $val['data'][$this->prefix . $this->sortBy]) . '_sortBy_';
            else
                $sortOrder = '';
            
            $stripped = $sortOrder . str_replace('{', '', $val['name']) . '_catId_' . $val['id'];
            
            // Determine depth
            $newDepth = substr_count($val['name'], '{');
            
            if ($newDepth > $depth) {
                
                $depthParents[$newDepth] = &$depthParents[$depth][$lastParent];
                
                $depth = $newDepth;
            }
            elseif ($newDepth < $depth) {
                
                $depthDiff = ($depth - $newDepth);
                
                $depthParents[$newDepth] = &$depthParents[($depth - $depthDiff)];
                
                $depth = $newDepth;
            }
            
            $depthParents[$depth][$stripped] = '';
            
            foreach ($val['data'] as $subKey => $subVal) {
                
                $newKey = str_replace($this->prefix, '', $subKey);
                
                $depthParents[$depth][$stripped]['data']['var::' . $newKey] = $subVal;
            }
            
            $depthParents[$depth][$stripped]['data']['var::count'] = count($val['data']);
            
            end($depthParents[$depth]);
            $lastParent = key($depthParents[$depth]);
            reset($depthParents[$depth]);
        }
        
        $out = $this->recursiveKeySort($out);
        
        return $out;
    }
    
    /**
     * Returns the full path to a single node
     * @param type $nodeId
     * @return type
     */
    public function singlePath($nodeId) {
        
        // Check if node exists
        if ($this->nodeExists($nodeId)) {
            
            // Retrieve the path
            $singlePath = $this->db->prepare("SELECT parent." . $this->prefix . "id as id, parent." . $this->prefix . "name as name, parent.*
                    FROM " . $this->table . " AS node,
                            " . $this->table . " AS parent
                    WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                            AND node." . $this->prefix . "id = ?
                    ORDER BY parent." . $this->prefix . "left", "i", array($nodeId));
            
            return $singlePath;
        }
    }
    
    public function subTree($nodeId, $type = 0) {
        
        $fetchType = ($type == 0) ? $this->prefix . "name" : "*";
        
        $fetchSubTree = $this->db->prepare("SELECT node." . $fetchType . ", (COUNT(parent." . $this->prefix . "id) - (sub_tree.depth + 1)) AS depth, node." . $this->prefix . "id as id, node.`" . $this->prefix . "left` as `myLeft`, node.`" . $this->prefix . "right` as `myRight`, (node.`" . $this->prefix . "right` - node.`" . $this->prefix . "left` + 1) as `myWidth`
                    FROM " . $this->table . " AS node,
                            " . $this->table . " AS parent,
                            " . $this->table . " AS sub_parent,
                            (
                                    SELECT node." . $this->prefix . "name, node." . $this->prefix . "id, (COUNT(parent." . $this->prefix . "id) - 1) AS depth
                                    FROM " . $this->table . " AS node,
                                            " . $this->table . " AS parent
                                    WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                                            AND node." . $this->prefix . "id = " . $nodeId . "
                                    GROUP BY node." . $this->prefix . "id
                                    ORDER BY node." . $this->prefix . "left
                            )AS sub_tree
                    WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                            AND node." . $this->prefix . "left BETWEEN sub_parent." . $this->prefix . "left AND sub_parent." . $this->prefix . "right
                            AND sub_parent." . $this->prefix . "id = sub_tree." . $this->prefix . "id
                    GROUP BY node." . $this->prefix . "id
                    ORDER BY node." . $this->prefix . "left");
        
        return $fetchSubTree;
    }
    
    /**
     * Counts the amount of nodes currently in the DB
     * Used by the constructor
     */
    private function count() {
        
        $select = $this->db->prepare("SELECT COUNT(*) as num FROM " . $this->table);
        
        $this->count = $select[0]['num'];
    }
    
    private function nodeCount($node) {
        
        // Gigantic query
        $childNodes = $this->db->prepare("SELECT node." . $this->prefix . "name, (COUNT(parent." . $this->prefix . "id) - (sub_tree.depth + 1)) AS depth
                    FROM " . $this->table . " AS node,
                            " . $this->table . " AS parent,
                            " . $this->table . " AS sub_parent,
                            (
                                    SELECT node." . $this->prefix . "id, (COUNT(parent." . $this->prefix . "id) - 1) AS depth
                                    FROM " . $this->table . " AS node,
                                            " . $this->table . " AS parent
                                    WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                                            AND node." . $this->prefix . "id = " . $node . "
                                    GROUP BY node." . $this->prefix . "id
                                    ORDER BY node." . $this->prefix . "left
                            )AS sub_tree
                    WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                            AND node." . $this->prefix . "left BETWEEN sub_parent." . $this->prefix . "left AND sub_parent." . $this->prefix . "right
                            AND sub_parent." . $this->prefix . "id = sub_tree." . $this->prefix . "id
                    GROUP BY node." . $this->prefix . "id
                    HAVING depth = 1
                    ORDER BY node." . $this->prefix . "left;");
        
        return count($childNodes);
    }
    
    public function nodeExists($id) {
        
        $checkNote = $this->db->prepare("SELECT `" . $this->prefix . "id` FROM `" . $this->table . "` WHERE `" . $this->prefix . "id`=?", "i", array($id));
        
        if (count($checkNote) == 0)
            return false;
        
        return true;
    }
    
    /**
     * Private function used by returnTree() to recursively sort array keys
     * @param array $array The array to sort
     * @return array Sorted array
     */
    private function recursiveKeySort($array) {
        
        foreach ($array as $key => $val) {
            
            if (is_array($val)) {
                
                $array[$key] = $this->recursiveKeySort($val);
            }
        }
        
        ksort($array);
        
        return $array;
    }
    
    /**
     * Fetches the entire tree from the database
     * Used in several update functions
     */
    private function selectTree() {
        
        $select = $this->db->prepare("SELECT CONCAT( REPEAT( '{', (COUNT(parent." . $this->prefix . "id) - 1) ), node." . $this->prefix . "name) AS name, `node`.`" . $this->prefix . "id` as `id`, `node`.*
                   FROM " . $this->table . " AS node,
                        " . $this->table . " AS parent
                   WHERE node." . $this->prefix . "left BETWEEN parent." . $this->prefix . "left AND parent." . $this->prefix . "right
                   GROUP BY node." . $this->prefix . "id
                   ORDER BY node." . $this->prefix . "left;");
        
        $newArray = array();
        
        foreach ($select as $key => $val) {
            
            $newArray[$key]['name'] = $val['name'];
            $newArray[$key]['id'] = $val['id'];
            $newArray[$key]['data'] = $val;
            
            unset($newArray[$key]['data']['name']);
            unset($newArray[$key]['data']['id']);
        }
        
        $this->tree = $newArray;
    }

    /**
     * Overwrites the entire selectTree function
     * Atm ony used for sitemaps
     */
    public function _selectTree($pageId) {

        // First grab the left & right
        $findNode = $this->db->prepare("SELECT * FROM " . $this->table . " WHERE `" . $this->prefix . "id`=?", "i", array($pageId));

        if (count($findNode) > 0) {

            $select = $this->db->prepare("SELECT CONCAT( REPEAT( '{', (COUNT(parent." . $this->prefix . "id) - 1) ), node." . $this->prefix . "name) AS name, `node`.`" . $this->prefix . "id` as `id`, `node`.*
                       FROM " . $this->table . " AS node,
                            " . $this->table . " AS parent
                       WHERE node." . $this->prefix . "left BETWEEN ? AND ?
                       GROUP BY node." . $this->prefix . "id
                       ORDER BY node." . $this->prefix . "left;", "ii", array($findNode[0][$this->prefix . 'left'], $findNode[0][$this->prefix . 'right']));
            
            $newArray = array();
            
            foreach ($select as $key => $val) {
                
                $newArray[$key]['name'] = $val['name'];
                $newArray[$key]['id'] = $val['id'];
                $newArray[$key]['data'] = $val;
                
                unset($newArray[$key]['data']['name']);
                unset($newArray[$key]['data']['id']);
            }
            
            $this->tree = $newArray;
        }
    }
}

?>
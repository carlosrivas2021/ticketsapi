<?php
global $queryCnt;
$queryCnt=0;
class usersSql
{
  public $_CACHE;
        function __construct()
        {
            $this->_CACHE=array();
//            echo '//SQL///';
        }
//$eventDB = new sql();
  //$eventDBconn = $eventDB->connect(E_DATABASE_SERVER, E_DATABASE_USER, E_DATABASE_PASSWORD, EM_DATABASE_NAME);
  //print_r($eventDB->get_row('SELECT * FROM pageLoad WHERE ID = "1"'));
  //echo $eventDB->get_cell('SELECT COLUMN_NAME FROM pageLoad WHERE ID = "1"');
  //echo $eventDB->getKeyColName('pageLoad','Events');
  //echo $eventDB->insertStuff('pageLoad',array('url'=>3));// returns new row ID
	//$knownGroups = $tixDB->listUnique('Tickets','typeCategories','groupName');// returns an array of unique values
  public $link;
	public $dblink;
	function connect($host, $user, $pass, $dbase){
    $this->link = mysqli_connect($host, $user, $pass);

		$this->dblink = $this->link;
		mysqli_set_charset($this->link, "utf8");
    if(!$this->link){return false;}

     //elseif(!){return false;}
    else{
			mysqli_select_db($this->link, $dbase);
			return $this->link;
		}

	}
	function clean($str){
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()){$str = stripslashes($str);}
    return mysqli_real_escape_string($this->link, $str);
	}
	function close(){mysqli_close($this->link);}



  function query($query){
    global $queryCnt;
    $queryCnt++;
		// echo $query.'  - cnt: '.$queryCnt.'<br>';
    $queryReserve = $query;
    $query = mysqli_query($this->link, $query);
    if(!$query){return false;}else{return $query;}
	}
  	// function query($query)
    //      {
    //          $hash=md5($query);
    //          if (!isset($this->_CACHE[$hash]))
    //          {
    //              global $queryCnt;
    //              $queryCnt++;
    //              echo $query.'  - cnt: '.$queryCnt.'<br>';
    //              $query2 = mysqli_query($this->link, $query);
    //              $this->_CACHE[$hash]=$query2;
    //              return $query2!==false?$query2:false;
    //          } else
    //              return $this->_CACHE[$hash];
  	// }


  function fetch_array($query){return @mysqli_fetch_array($query,MYSQLI_ASSOC);}
  function fetch_field($query, $offset){
    $query = mysqli_fetch_field($query, $offset);
    if(!$query){echo "SQL query failed: ".mysqli_error($this->link);return false;}
    else{return $query;}
  }
  function getKeyColName($tabelName,$dbname = 'Synergy'){//replace with default table name
    $query = "
    SELECT `COLUMN_NAME`
    FROM `information_schema`.`COLUMNS`
    WHERE (`TABLE_SCHEMA` = '".$dbname."')
      AND (`TABLE_NAME` = '".$tabelName."')
      AND (`COLUMN_KEY` = 'PRI');
    ";
    $colName = @mysqli_fetch_array($this->query($query),MYSQLI_BOTH);
    return $colName['COLUMN_NAME'];
  }
  function insertStuff($table,$data,$dbname = 'Synergy'){// $data is an array of values keyed by column name
    $keyCol = $this->getKeyColName($table,$dbname);
    $s1 = $s2 = "";
    foreach($data as $k => $v){if($k != $keyCol){$s1 .= $k.","; $s2 .= "'".$this->clean($v)."',";}}
    $s1 = rtrim($s1,',');$s2 = rtrim($s2,',');
    $query = "INSERT IGNORE INTO $table (".$s1.") VALUES (".$s2.")";
    $results = @mysqli_query($this->link, $query);
    if($results){return mysqli_insert_id($this->link);}
  }
	function replaceStuff($table,$data,$dbname = 'Synergy'){// $data is an array of values keyed by column name
		$keyCol = $this->getKeyColName($table,$dbname);
		$s1 = $s2 = "";
		foreach($data as $k => $v){if($k != $keyCol){$s1 .= $k.","; $s2 .= "'".$this->clean($v)."',";}}
		$s1 = rtrim($s1,',');$s2 = rtrim($s2,',');
		$query = "REPLACE INTO $table (".$s1.") VALUES (".$s2.")";
		$results = @mysqli_query($this->link, $query);
		if($results){return mysqli_insert_id($this->link);}
	}
	function multiInsertStuff($table,$data,$dbname = 'Synergy'){// expect multi dimensional array as $data where each subarray has identical structure
    $keyCol = $this->getKeyColName($table,$dbname);
    $s1 = $s2 = "";

		// build insert and keys from first element
		$map = $data[0];
    foreach($map as $k => $v){if($k != $keyCol){$s1 .= $k.",";}}
    $s1 = rtrim($s1,',');$s2 = rtrim($s2,',');
    $query = "INSERT IGNORE INTO $table (".$s1.") VALUES ";

		foreach($data as $k => $v){
			$s2 = '';
			foreach($v as $kk => $vv){
				if($kk != $keyCol){$s2 .= "'".$this->clean($vv)."',";}
			}
			$s2 = rtrim($s2,',');
			$query .= "(".$s2."),";
		}

		$query = rtrim($query,',');
// 		return json_encode($map);
    $results = @mysqli_query($this->link, $query);
    if($results){return mysqli_insert_id($this->link);}
  }
  function fetch_row($query){return mysqli_fetch_row($query);}
	function update_cell($table,$set,$where){return $this->query("UPDATE $table SET $set WHERE $where");}//$eventDB->update_cell('pageLoad',"duration='100'","ID='5'");
  function field_name($query, $offset){return mysqli_field_name($query, $offset);}
  function free_result($query){mysqli_free_result($query);}
  function insert_id(){return mysqli_insert_id($this->link);}
  function num_fields($query){return mysqli_num_fields($query);}
  function num_rows($query){ return mysqli_num_rows($query);}
  function real_escape_string($string){  return mysqli_real_escape_string($string, $this->link);  }
  function get_row($query){return @mysqli_fetch_array($this->query($query),MYSQLI_BOTH);}
  function get_cell($query){$query = @mysqli_fetch_array($this->query($query),MYSQLI_BOTH);return is_array($query) ? $query[0] : "";}
	function countResults($query){$count = @mysqli_fetch_array($this->query($query),MYSQLI_NUM);return $count[0];}
	function getTables($dbase){mysqli_select_db($this->dblink, $dbase);$listdbtables = array_column(mysqli_fetch_all($this->dblink->query('SHOW TABLES')),0);return $listdbtables;}

	function affected_rows(){return mysqli_affected_rows($this->link);}
	function escape_string($string){return mysqli_escape_string($string);}
	function listUnique($db,$table,$col){
		$arrayOfUniqueValuesInColumn = array();
		$theseValues = @mysqli_fetch_array($this->query('SELECT DISTINCT '.$col.' FROM '.$db.'.'.$table),MYSQLI_NUM);
		if(is_array($theseValues) && count($theseValues) > 0){$arrayOfUniqueValuesInColumn = $theseValues;}
		return $arrayOfUniqueValuesInColumn;
	}
// 	function fetch_row($query){return mysqli_fetch_row($query);}

// 	function field_name($query, $offset){return mysqli_field_name($query, $offset);}

// 	function free_result($query){mysqli_free_result($query);}

// 	function insert_id(){return mysqli_insert_id($this->link);}

// 	function num_fields($query){return mysqli_num_fields($query);}

// 	function num_rows($query){return mysqli_num_rows($query);}

// 	function fetch_array($query){return @mysqli_fetch_array($query,MYSQLI_ASSOC);}
}

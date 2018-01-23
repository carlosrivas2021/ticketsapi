<?php
global $_DB_CACHE;
$_DB_CACHE=array();
function is_user_logged_in()
{
  return (isset($_SESSION['current_user']));
}

// class GT_Mem_Cache
// {
// 	public static $_CACHE=array();
// }

/**
 * T_Global_Tech_Object Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_Object
{
  /**
	 * description field
	 *
	 * @var	array
	 */
  protected  $_fields=array();

  /**
	 * description field
	 *
	 * @var	array
	 */
  protected $_data=array();

  /**
	 * description field
	 *
	 * @var	bool
	 */
  protected $_empty;
  
  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
	 * @return	void
	 */
  function __construct($id=0)
  {
    if ($id!=0)
      $this->load($id);
    else
      $this->emptyObject();
  }

	public function isEmpty()
	{
		return $this->_empty;
  }
  
  public function equals($object)
  {
    if (!($object instanceof T_Global_Tech_Object))
      return false;
    return ($this->_data==$object->_data);
  }

  public function emptyObject()
  {
    foreach($this->_fields as $f)
        $this->_data[$f]='';
		$this->_empty=true;
  }

  public function get($key)
  {
    return (isset($this->_data[$key]))?$this->_data[$key]:'';
  }

  public function getFields()
  {
    return $this->_fields;
  }

  public function set($key, $value)
  {
    $this->_data[$key]=$value;
  }

  public function load($id)
  {   }

  public function setAll($data)
  {
    foreach($this->_fields as $f)
      if (isset($data[$f]))
				$this->set($f,$data[$f]);
  }

}

/**
 * T_Global_Tech_DB_Object Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_DB_Object extends T_Global_Tech_Object
{
  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_db_table;

  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_meta_table;

  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_meta_idx;

  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_meta;

  /**
	 * description field
	 *
	 * @var	¿?
	 */
  protected $_alt_db=null;
  
  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
   * @param 	string	      $by	¿description?
   * @param 	array	        $row ¿description?
	 * @return	void
	 */
  function __construct($id, $by, $row=array())
  {
    $this->_meta=array();
    if (count($row)==0)
    {
      if ($by=='ID')
      	parent::__construct($id);
      else
        $this->loadBy($id, $by);
    } else
    {
      foreach($this->_fields as $field)
      if (isset($row[$field]))
      {
        $this->set($field, $row[$field]);
        unset($row[$field]);
      }
      $this->_meta=$row;
      $this->_empty=false;
    }
  }

  public function getMetaTable()
  {
    return $this->_meta_table;
  }

  public function getMetaIdx()
  {
    return $this->_meta_idx;
  }

	function __clone()
  {
   	$this->set('ID','');
    $this->save();
  }

  public function get($k)
  {
    $v=parent::get($k);
    if (($v=='')&&(isset($this->_meta[$k])))
      $v=$this->_meta[$k];
    return $v;
  }

  public function set($key, $value)
  {
    if (in_array($key, $this->_fields))
    	parent::set($key,$value);
    else
      $this->_meta[$key]=$value;
  }

  public function getMeta()
  {
    $rv=array();
    if ($this->_meta_table!='')
    {
      global $_DB_CACHE;
      if (!isset($_DB_CACHE[$this->_meta_table]))
        $_DB_CACHE[$this->_meta_table]=array();
      $q="SELECT * FROM ".$this->_meta_table." WHERE ".$this->_meta_idx.'="'.$this->get('ID').'" ORDER BY ID ASC';
      $hash=md5($q);
      if (!isset($_DB_CACHE[$this->_meta_table][$hash]))
      {
  			$usersDB=$this->_getDB();
      	$query=$usersDB->query($q);
        while($row=$usersDB->fetch_array($query))
        	$rv[$row['meta_key']]=$row['meta_value'];
        $_DB_CACHE[$this->_meta_table][$hash]=$rv;
      } else
        $rv=$_DB_CACHE[$this->_meta_table][$hash];
    }
    return $rv;
  }

  public function getAllMeta()
  {
    $rv=array();
    if ($this->_meta_table!='')
    {
      global $_DB_CACHE;
      if (!isset($_DB_CACHE[$this->_meta_table]))
        $_DB_CACHE[$this->_meta_table]=array();
      $q="SELECT * FROM ".$this->_meta_table." WHERE ".$this->_meta_idx.'="'.$this->get('ID').'" ORDER BY meta_key ASC, created DESC';
      $hash=md5($q);
      if (!isset($_DB_CACHE[$this->_meta_table][$hash]))
      {
  			$usersDB=$this->_getDB();
      	$query=$usersDB->query($q);
        while($row=$usersDB->fetch_array($query))
  			{
  				if (!isset($rv[$row['meta_key']]))
  					$rv[$row['meta_key']]=array();
  				$rv[$row['meta_key']][]=array('editor'=>$row['editor'],'created'=>$row['created'], 'meta_value'=>$row['meta_value']);
  			}
        $_DB_CACHE[$this->_meta_table][$hash]=$rv;
      } else
        $rv=$_DB_CACHE[$this->_meta_table][$hash];
    }
    return $rv;
  }

	protected function _getDB()
	{
		if (is_null($this->_alt_db))
		{
			global $usersDB;
			return $usersDB;
		} else
			return $this->_alt_db;
  }
  
	protected function _setDB($db)
	{
		$this->_alt_db=$db;
  }
  
  public function loadBy($value, $by)
  {
    global $_DB_CACHE;
    if (!isset($_DB_CACHE[$this->_db_table]))
      $_DB_CACHE[$this->_db_table]=array();
    $q="SELECT * FROM ".$this->_db_table." WHERE ".$by.'="'.$value.'" LIMIT 1';
    $hash=md5($q);
    if (!isset($_DB_CACHE[$this->_db_table][$hash]))
    {
      $usersDB2=$this->_getDB();
      $query=$usersDB2->query($q);
      if ($usersDB2->num_rows($query)>0)
      {
        $row=$usersDB2->fetch_array($query);
        $_DB_CACHE[$this->_db_table][$hash]=$row;
      } else
      	throw new Exception(get_class($this).' not found. (By '.$by.', '.$value.')');
    } else {
      $row=$_DB_CACHE[$this->_db_table][$hash];
    }

    foreach($this->_fields as $field)
      $this->set($field, $row[$field]);

    $this->_meta=$this->getMeta();
    $this->_empty=false;
  }

  public function load($id)
  {
    $this->loadBy($id, 'ID');
  }

	public function delete()
	{
		$usersDB=$this->_getDB();
		if ($this->get('ID')!='')
		{
			$query=$usersDB->query('DELETE FROM '.$this->_db_table.' WHERE ID='.$this->get('ID'));
			if ($query!==false)
			{
				$this->emptyObject();
				return true;
			}
		}
		return false;
  }
  
  public function save()
  {

		global $current_user;
    $usersDB=$this->_getDB();
		$dt=date('Y-m-d H:i:s');
		$editor=isset($current_user)&&($current_user instanceof self)?$current_user->get('ID'):0;

    $fields=''; $values='';
    foreach($this->_data as $k=>$v)
      if ($k!='ID')
      {
        if ($this->get('ID')=='')
        {
          $values.='"'.$usersDB->clean($v).'",';
          $fields.=$k.',';
        } else
        	$fields.=$k.'="'.$usersDB->clean($v).'",';
      }
    $fields=rtrim($fields, ','); $values=rtrim($values,',');

    $sql=($this->get('ID')=='') ?
      'INSERT INTO '.$this->_db_table.'('.$fields.') VALUES('.$values.')':
    	'UPDATE '.$this->_db_table.' SET '.$fields.' WHERE ID='.$this->get('ID');

    $result=$usersDB->query($sql);
    global $_DB_CACHE;
    $_DB_CACHE[$this->_db_table]=array();
    if ($result!==false)
    {
      if ($this->get('ID')=='')
    		$this->set('ID',$usersDB->insert_id());
    } else
      return false;

		$tempMeta=$this->getMeta();
    foreach($this->_meta as $k=>$v)
    {
			$sql='';
      if (isset($tempMeta[$k]))
      {
				if ($v!=$tempMeta[$k])
					$sql='INSERT INTO '.$this->_meta_table.'('.$this->_meta_idx.', meta_key, meta_value, editor, created) VALUES('.$this->get('ID').',"'.$k.'","'.$usersDB->clean($v).'",'.$editor.',"'.$dt.'")';
      } else
        $sql='INSERT INTO '.$this->_meta_table.'('.$this->_meta_idx.', meta_key, meta_value, editor, created) VALUES('.$this->get('ID').',"'.$k.'","'.$usersDB->clean($v).'",'.$editor.',"'.$dt.'")';
      if ($sql!='')
				$q=$usersDB->query($sql);
        global $_DB_CACHE;
        $_DB_CACHE[$this->_meta_table]=array();
    }
    return $this->get('ID');
  }

  function setAll($data)
  {
    if (isset($data['ID']))
      unset($data['ID']);
    parent::setAll($data);
  }
}

/**
 * T_Global_Tech_DB_Cross_Object Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_DB_Cross_Object extends T_Global_Tech_DB_Object
{
  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
   * @param 	string	      $by	¿description?
   * @param 	string	      $orderBy	¿description?
   * @param 	array	        $row ¿description?
	 * @return	void
	 */
	function __construct($id, $by, $orderBy='', $row=array())
  {
		if ((!(is_array($id)&&is_array($by)))||(count($row)>0))
    {
      // if (($this->_db_table=='tours')&&($id==149))
      // { echo '//e//'; var_dump($row); echo '//f//'; var_dump(debug_backtrace()); die; }
			parent::__construct($id, $by,  $row);
    } else
		{
    	$this->_meta=array();
      $this->loadByMultiple($id, $by, $orderBy);
		}
  }

	function loadByMultiple($values, $by, $orderBy='')
	{
		$usersDB=$this->_getDB();
		$where=' ';
		foreach($by as $k=>$b)
			if (isset($values[$k]))
				$where.=$b.'="'.$values[$k].'" AND ';
			$where=rtrim($where, ' AND ');
		if ($where!=' ')
			$where=' WHERE '.$where;
		if (trim($orderBy)!='')
      $where.=' ORDER BY '.$orderBy;
      
    global $_DB_CACHE;
    if (!isset($_DB_CACHE[$this->_db_table]))
      $_DB_CACHE[$this->_db_table]=array();
    $q="SELECT * FROM ".$this->_db_table.$where.'LIMIT 1';
    $hash=md5($q);
    $row=false;
    if (!isset($_DB_CACHE[$this->_db_table][$hash]))
    {
      $query=$usersDB->query($q);
      if ($usersDB->num_rows($query)>0)
      {
        $row=$usersDB->fetch_array($query);
        $_DB_CACHE[$this->_db_table][$hash]=$row;
      }  else
  			$this->emptyObject();
    } else {
      $row=$_DB_CACHE[$this->_db_table][$hash];
    }
    if ($row!==false)
    {
      foreach($this->_fields as $field)
        $this->set($field, $row[$field]);
      $this->_meta=$this->getMeta();
			$this->_empty=false;
    }
	}
}

/**
 * T_Global_Tech_DB_Hierarchical_Object Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_DB_Hierarchical_Object extends T_Global_Tech_DB_Object
{
  /**
	 * description field
	 *
	 * @var	¿?
	 */
  protected $_lister;

  /**
	 * description field
	 *
	 * @var	¿?
	 */
  private $_children;

  /**
	 * description field
	 *
	 * @var	¿?
	 */
  private $_allchildren;
  
  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
   * @param 	string	      $by	¿description?
   * @param 	array	        $row ¿description?
	 * @return	void
	 */
  function __construct($id, $by, $row=array())
  {
    $this->_children=null;
	  $this->_allchildren=null;
    parent::__construct($id,$by, $row);
  }

	public function getBranch()
	{
		$rv=array();
		$item=$this;
		$rv[]=$item;
		$lister=new $this->_lister();
		$class=$lister->getClass();
		while($item->get('parent')!=0)
		{
			$item=new $class($item->get('parent'));
			$rv[]=$item;
		}
		return $rv;
  }
  
  function __clone()
  {
    $_oldId=$this->get('ID');
    $children=$this->getChildren();
    parent::__clone();

    foreach($children as $child)
    {
      $newObject = clone $child;
      $newObject->set('parent', $this->get('ID'));
      $newObject->save();
    }
  }

  public function hasChildren()
  {
    return (count($this->getChildren())!=0);
  }

  public function getChildren()
  {
    if (is_null($this->_children))
    {
      $this->_children=(new $this->_lister($this->get('ID'),'parent'))->getList();
    }
    return $this->_children;
  }

  public function getAllChildren($rv=array())
  {
	  // die('bad function.');

    if (is_null($this->_allchildren))
    {
      $children=$this->getChildren();
      foreach($children as $child)
        $rv=$child->getAllChildren($rv);
      $rv[]=$this;

      $this->_allchildren=$rv;
    }
    return $this->_allchildren; //$rv;
  }

	public function getAllChildrenDB($rv=array())
  {
    if (is_null($this->_allchildren))
    {
      $children=$this->getChildren();
      foreach($children as $child)
        $rv=$child->getAllChildren($rv);
      $rv[]=$this;

      $this->_allchildren=$rv;
    }
    return $this->_allchildren; //$rv;
  }

  public function getAllChildrenJohn($rv=array())
  {
    if (count($rv)==0)
    {
      $usersDB=$this->_getDB();
      $rv=array();
      $tmp=array();
      $tmp2=array();

      $query=$usersDB->query(trim('SELECT * FROM '.$this->_db_table));
      while($row=$usersDB->fetch_array($query))
      {
        if (!isset($tmp[$row['parent']]))
          $tmp[$row['parent']]=array();
        $tmp[$row['parent']][]=$row;
      }
      $rv=$tmp;
    }
    // foreach($tmp as $k=>$v)
    // {
    //   if ($v['parent']==$this->get('ID'))
    //     $tmp2[]	=
    // }
  }

	public function getLeaves($rv=array())
	{
		if (!$this->hasChildren())
			$rv[]=$this;
		else
		{
			$children=$this->getChildren();
			foreach($children as $child)
				$rv=$child->getLeaves($rv);
		}

		return $rv;
  }
  
  public function save()
  {
    $this->_children=null;
    parent::save();
  }

  public function delete()
  {
    if ($this->hasChildren())
      foreach($this->getChildren() as $child)
        $child->delete();
    return parent::delete();
  }
}

/**
 * T_Global_Tech_Object_List Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_Object_List
{
  /**
	 * description field
	 *
	 * @var	array
	 */
  protected $_items;
  
  /**
	 * description field
	 *
	 * @var	¿¿¿integer???
	 */
  private $_pointer;

  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
   * @param 	array	        $item ¿description?
	 * @return	void
	 */
  function __construct($items=array())
  {
    $this->_pointer=0;
    $this->_items=$items;
  }

	public function find($key, $value)
	{
		foreach($this->_items as $k=>$i)
			if ($i->get($key)==$value)
				return $i;
		return false;
  }
  
  public function inList($item)
  {
    if ($item instanceof T_Global_Tech_Object)
      foreach($this->_items as $k=>$i)
        if ($i->equals($item))
          return true;

    return false;
  }

  public function add($item)
  {
    if ($this->inList($item)===false)
      $this->_items[]=$item;
  }

  public function getList($filters=array())
  {
		if (count($filters)==0)
    	return $this->_items;
		$rv=$this->_items;

		foreach($filters as $filter)
			foreach($rv as $k=>$item)
				if (!$this->_passFilterCheck($item, $filter))
					unset($rv[$k]);

		return $rv;
  }

  public function getDropdownList($filters=array())
  {
		// if (count($filters)==0)
    // 	return $this->_items;
		$rv=$this->_items;
	  	$rrv=array();

		foreach($filters as $filter)
			foreach($rv as $k=>$item)
				if (!$this->_passFilterCheck($item, $filter))
					unset($rv[$k]);
		foreach($rv as $v)
			$rrv[$v->get('ID')]=$v->get('name');
	  return $rrv;
  }

	// static function sortCmp($a, $b)
	// {
	// 	return;
  // }
  
	public function sortBy($key='ID',$order='ASC')
	{
		// usort($fruits, "cmp");
	}

	protected function _passFilterCheck($item, $filter)
	{
		$op=isset($filter['op'])?$filter['op']:'=';
		$key=isset($filter['key'])?$filter['key']:'';
		$value=isset($filter['value'])?$filter['value']:'';

		if (($key=='')||($value==''))
			return true;

		if (!($item instanceof $this->_class))
			return false;
		switch($op)
		{
			case '==':
				return $item->get($key)===$value;
				break;
			case '=':
				return $item->get($key)==$value;
				break;
			case '!=':
				return $item->get($key)!=$value;
				break;
			case '>':
				return $item->get($key)>$value;
				break;
			case '<':
				return $item->get($key)<$value;
				break;
			case '>=':
				return $item->get($key)>=$value;
				break;
			case '<=':
				return $item->get($key)<=$value;
				break;
			default:
				return $item->get($key)==$value;
				break;
		}
	}
}

/**
 * T_Global_Tech_DB_Object_List Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_DB_Object_List extends T_Global_Tech_Object_List
{
  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_db_table;

  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_class=null;

  /**
	 * description field
	 *
	 * @var	array
	 */
  protected $_fields;

  /**
	 * description field
	 *
	 * @var	string
	 */
  protected $_alt_db;
  
  /**
	 * description field
	 *
	 * @var	¿¿¿array???
	 */
  public $_all_meta;

  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
   * @param 	string	      $by	¿description?
   * @param 	string	      $orderBy	¿description?
	 * @return	void
	 */
  function __construct($id, $by, $orderBy='')
  {
    $tmp=new $this->_class();
    // var_dump($this->_class);
    $metaTable=$tmp->getMetaTable();
    $metaIdx=$tmp->getMetaIdx();
    $this->_all_meta=array();

    if (($metaTable!='')&&($metaIdx!=''))
    {
      $usersDB=$this->_getDB();
      $rv=array();

      global $_DB_CACHE;
      if (!isset($_DB_CACHE[$metaTable]))
        $_DB_CACHE[$metaTable]=array();
      $q=trim('SELECT * FROM '.$metaTable.' ORDER BY ID ASC');
      $hash=md5($q);
      $row=false;
      if (!isset($_DB_CACHE[$metaTable][$hash]))
      {
        $query=$usersDB->query($q);
        while($row=$usersDB->fetch_array($query))
        {
          if (!isset($this->_all_meta[$row[$metaIdx]]))
            $this->_all_meta[$row[$metaIdx]]=array();
          $this->_all_meta[$row[$metaIdx]][$row['meta_key']]=$row['meta_value'];
        }
        $_DB_CACHE[$metaTable][$hash]=$this->_all_meta;
      } else {
        $this->_all_meta=$_DB_CACHE[$metaTable][$hash];
      }
    }
    $where='';
    if ((in_array($by,$this->_fields))&&($id!=''))
			$where.=' WHERE '.$this->_db_table.'.'.$by.'="'.$id.'"';

		if (trim($orderBy)!='')
			$where.=' ORDER BY '.$orderBy;

    // var_dump($where);
    parent::__construct($this->_load($where));
    $rv=array();
    foreach($this->_items as $i)
      $rv[$i->get('ID')]=$i;
    $this->_items=$rv;
  }

	public function getFields()
	{
		return $this->_fields;
  }
  
	protected function _getDB()
	{
		if (is_null($this->_alt_db))
		{
			global $usersDB;
			return $usersDB;
		} else
			return $this->_alt_db;
  }
  
	protected function _setDB($db)
	{
		$this->_alt_db=$db;
  }
  
	public function getClass()
	{
		return $this->_class;
  }
  
  protected function _load($where)
  {
    $usersDB=$this->_getDB();
    $rv=array();

    global $_DB_CACHE;
    if (!isset($_DB_CACHE[$this->_db_table]))
      $_DB_CACHE[$this->_db_table]=array();
    $q=trim('SELECT * FROM '.$this->_db_table.' '.$where);
    $hash=md5($q);

    if (!isset($_DB_CACHE[$this->_db_table][$hash]))
    {
      $query=$usersDB->query($q);
      while($row=$usersDB->fetch_array($query))
      {
        $am=isset($this->_all_meta[$row['ID']])?$this->_all_meta[$row['ID']]:array();
        $full_row=$row+$am;
        $rv[]=new $this->_class($row['ID'], 'ID', $full_row);
      }
      $_DB_CACHE[$this->_db_table][$hash]=$rv;
    } else {
      $rv=$_DB_CACHE[$this->_db_table][$hash];
    }
    return $rv;
  }
}

/**
 * T_Global_Tech_DB_Cross_Object_List Class
 *
 * Description.
 *
 * @author	-
 * @license	-
 * @link	-
 */
class T_Global_Tech_DB_Cross_Object_List extends T_Global_Tech_DB_Object_List
{
  /**
	 * Class constructor
	 *
	 * Set connection data and create connector. 
   * 
	 * @param 	¿int|string?	$id	¿description?
   * @param 	string	      $by	¿description?
   * @param 	string	      $orderBy	¿description?
	 * @return	void
	 */
	function __construct($id, $by, $orderBy='')
  {
		if (!(is_array($id)&&is_array($by)))
    {
			parent::__construct($id, $by, $orderBy);
    }
		else
		{
      $this->_all_meta=array();
      $tmp=new $this->_class();
      // var_dump($this->_class);
      $metaTable=$tmp->getMetaTable();
      $metaIdx=$tmp->getMetaIdx();
      if (($metaTable!='')&&($metaIdx!=''))
      {
        $usersDB=$this->_getDB();
        $rv=array();
        global $_DB_CACHE;
        if (!isset($_DB_CACHE[$metaTable]))
          $_DB_CACHE[$metaTable]=array();
        $q=trim('SELECT * FROM '.$metaTable.' ORDER BY ID ASC');
        $hash=md5($q);
        $row=false;
        if (!isset($_DB_CACHE[$metaTable][$hash]))
        {
          $query=$usersDB->query($q);
          while($row=$usersDB->fetch_array($query))
          {
            if (!isset($this->_all_meta[$row[$metaIdx]]))
              $this->_all_meta[$row[$metaIdx]]=array();
            $this->_all_meta[$row[$metaIdx]][$row['meta_key']]=$row['meta_value'];
          }
          $_DB_CACHE[$metaTable][$hash]=$this->_all_meta;
        } else {
          $this->_all_meta=$_DB_CACHE[$metaTable][$hash];
        }
      }

    	$this->_meta=array();
      $this->_items=$this->loadByMultiple($id, $by, $orderBy);
			$rv=array();
	    foreach($this->_items as $i)
  	    $rv[$i->get('ID')]=$i;
    	$this->_items=$rv;
		}
  }

	function loadByMultiple($values, $by, $orderBy='')
	{
		$usersDB=$this->_getDB();
		$where=' ';
		foreach($by as $k=>$b)
			if (isset($values[$k]))
				$where.=$b.'="'.$values[$k].'" AND ';
			$where=rtrim($where, ' AND ');
		if ($where!=' ')
			$where=' WHERE '.$where;
		if (trim($orderBy)!='')
			$where.=' ORDER BY '.$orderBy;
		// echo '//2//';
		// var_dump($orderBy);
		// var_dump($where);
		return $this->_load($where);
	}
}
<?php
class GT_Role extends T_Global_Tech_DB_Object
{
  function __construct($id=0, $by='ID',$row=array())
  {
    $this->_db_table='roles';
    $this->_fields=array('ID', 'appClientID',  'name', 'created_at','created_by','description');
    parent::__construct($id, $by, $row);
  }
  
}
class GT_Role_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='')
	{
            $this->_db_table='roles';
            $this->_fields=array('ID', 'appClientID',  'name', 'created_at','created_by','description');
            $this->_class='GT_Role';

            parent::__construct($id, $by);
	}
}
?>

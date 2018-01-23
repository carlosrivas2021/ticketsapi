<?php
class GT_Client extends T_Global_Tech_DB_Object
{
  function __construct($id=0, $by='ID',$row=array())
  {
    $this->_db_table='clients';
    $this->_fields=array('ID', 'name');
    parent::__construct($id, $by, $row);
  }
  
}
class GT_Client_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='')
	{
		$this->_db_table='clients';
		$this->_fields=array('ID', 'name');
		$this->_class='GT_Client';

		parent::__construct($id, $by);
	}
}
?>

<?php
class GT_Attribute extends T_Global_Tech_DB_Cross_Object
{
  function __construct($id=0, $by='ID',$row=array())
  {
    $this->_db_table='attributes';
    $this->_fields=array('ID', 'objectID','objectType', 'name', 'value');
    parent::__construct($id, $by,'', $row);
  }
}
class GT_Attribute_List extends T_Global_Tech_DB_Cross_Object_List
{
	function __construct($id='', $by='')
	{
		$this->_db_table='attributes';
		$this->_fields=array('ID', 'objectID','objectType', 'name', 'value');
		$this->_class='GT_Attribute';

		parent::__construct($id, $by);
	}
}

?>

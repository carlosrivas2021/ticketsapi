<?php
class GT_X_User_Client extends T_Global_Tech_DB_Cross_Object
{
  function __construct($id=0, $by='ID', $row=array())
  {
    $this->_db_table='x_users_clients';
    $this->_fields=array('ID', 'userID', 'clientID', 'groupID');
    parent::__construct($id, $by,'', $row);
  }
}

class GT_X_User_Client_List extends T_Global_Tech_DB_Cross_Object_List
{
	function __construct($id='', $by='userID')
	{
		$this->_db_table='x_users_clients';
    $this->_fields=array('ID', 'userID', 'clientID', 'groupID');
    $this->_class='GT_X_User_Client';

		parent::__construct($id, $by);
	}
}

?>

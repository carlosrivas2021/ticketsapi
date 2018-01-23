<?php
class GT_User_Password extends T_Global_Tech_DB_Object
{
  function __construct($id=0, $by='ID',$row=array())
  {
    $this->_db_table='users_password';
    $this->_fields=array('ID', 'userID', 'password', 'appClientID', 'updated_at');
    parent::__construct($id, $by, $row);
  }
  
}
class GT_User_Password_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='')
	{
            $this->_db_table='users_password';
            $this->_fields=array('ID', 'userID', 'password', 'appClientID', 'updated_at');
            $this->_class='GT_User_Password';

            parent::__construct($id, $by);
	}
}
?>

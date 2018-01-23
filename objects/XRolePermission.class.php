<?php
class GT_X_Role_Permission extends T_Global_Tech_DB_Cross_Object
{
  function __construct($id=0, $by='ID', $row=array())
  {
    $this->_db_table='x_roles_permissions';
    $this->_fields=array('ID', 'roleID', 'permissionID');
    parent::__construct($id, $by,'', $row);
  }
}

class GT_X_Role_Permission_List extends T_Global_Tech_DB_Cross_Object_List
{
	function __construct($id='', $by='userID')
	{
            $this->_db_table='x_roles_permissions';
            $this->_fields=array('ID', 'roleID', 'permissionID');
            $this->_class='GT_X_Role_Permission';
            parent::__construct($id, $by);
	}
}

?>

<?php
class GT_X_User_Role extends T_Global_Tech_DB_Cross_Object
{
  function __construct($id=0, $by='ID', $row=array())
  {
    $this->_db_table='x_users_roles';
    $this->_fields=array('ID', 'userID', 'roleID', 'appClientID');
    parent::__construct($id, $by,'', $row);
  }
}

class GT_X_User_Role_List extends T_Global_Tech_DB_Cross_Object_List
{
    function __construct($id='', $by='userID')
    {
        $this->_db_table='x_users_roles';
        $this->_fields=array('ID', 'userID', 'roleID', 'appClientID');
        $this->_class='GT_X_User_Role';
        parent::__construct($id, $by);
    }
}
?>

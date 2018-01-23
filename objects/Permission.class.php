<?php
class GT_Permission extends T_Global_Tech_DB_Cross_Object
{
  function __construct($id=0, $by='ID',$row=array())
  {
    $this->_db_table='permissions';
    $this->_fields=array('ID','slug','name', 'parent', 'root','app','description');
    parent::__construct($id, $by,'', $row);
  }
  public function get($key)
  {
    if (($key=='active')&&($this->get('objectType')=='app'))
        return '1';
    return parent::get($key);
  }

  public function save()
  {
    if (trim($this->get('slug'))=='')
      $this->set('slug', $this->__sanitize($this->get('name')));
    else
      $this->set('slug', $this->__sanitize($this->get('slug')));

    parent::save();
  }
  protected function __sanitize($text)
  {
    return strtolower(preg_replace("/[^a-zA-Z0-9_]+/", "", str_replace(' ','_',$text)));
  }

}
class GT_Permission_List extends  T_Global_Tech_DB_Cross_Object_List
{
	function __construct($id='', $by='')
	{

		$this->_db_table='permissions';
    $this->_fields=array('ID','slug','name', 'parent', 'root','app','description');
		$this->_class='GT_Permission';

		parent::__construct($id, $by);
	}
}
?>

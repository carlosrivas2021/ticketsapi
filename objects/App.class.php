<?php
class GT_App extends T_Global_Tech_DB_Object
{
  function __construct($id=0, $by='ID', $row=array())
  {
    $this->_db_table='apps';
    $this->_fields=array('ID', 'name', 'api_key');
    parent::__construct($id, $by, $row);
	}
	
	public function getPermissionsTree($rv=array())
	{
		$atts=(new GT_Permission_List(array($this->get('ID'), 'app'), array('objectID','objectType') ))->getList();
    foreach($atts as $att)
    	$rv[$att->get('slug')]=array('depth'=>1,'value'=>$att->get('active'),'id'=>$att->get('ID'), 'label'=>$att->get('name'), 'oid'=>$att->get('objectID'), 'type'=>$att->get('objectType'));
		return $rv;
	}

	public function getAttsTree($rv=array())
	{
		$atts=(new GT_Attribute_List(array($this->get('ID'), 'app'), array('objectID','objectType') ))->getList();
    foreach($atts as $att)
    	$rv[$att->get('name')]=array('value'=>$att->get('value'),'id'=>$att->get('ID'));
		return $rv;
	}
}

class GT_App_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='')
	{
		$this->_db_table='apps';
		$this->_fields=array('ID', 'name', 'api_key');
		$this->_class='GT_App';

		parent::__construct($id, $by);
	}
}

?>

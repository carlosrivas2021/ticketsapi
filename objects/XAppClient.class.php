<?php
class GT_X_App_Client extends T_Global_Tech_DB_Cross_Object
{
	protected $_atts;
	protected $_user;
	protected $_groups;
  function __construct($id=0, $by='ID', $row=array())
  {
		$this->_atts=null;
		$this->_user=null;
		$this->_groups=null;

    $this->_db_table='x_apps_clients';
    $this->_fields=array('ID', 'appID', 'clientID', 'domain');
    parent::__construct($id, $by,'', $row);
  }
	public function getAttsTree($rv=array())
	{
		if (is_null($this->_atts))
		{
			if (is_null($this->_user))
			{
				$app=new GT_App($this->get('appID'));

				$atts=(new GT_Attribute_List(array($app->get('ID'), 'app'), array('objectID','objectType') ))->getList();
				foreach($atts as $att)
					$rv[$att->get('name')]=array('value'=>$att->get('value'),'id'=>$att->get('ID'));

				$atts=(new GT_Attribute_List(array($this->get('ID'), 'client'), array('objectID','objectType') ))->getList();
				foreach($atts as $att)
					$rv[$att->get('name')]=array('value'=>$att->get('value'),'id'=>$att->get('ID'));
				$this->_atts=$rv;
			} else
			{
				$found=false;
				foreach($this->_user->getGroups() as $group)
					if ($group->get('appClientID')==$this->get('ID'))
					{
						$this->_atts=$group->getAttsTree();
						$found=true;
					}
				if (!$found)
				{
					trigger_error('Error, attached user does not belong to any groups in this App. Falling back to App attributes.', E_USER_NOTICE);
					$this->_user=null;
					return $this->getAttsTree();
				}
			}
		}

		return $this->_atts;
	}
	public function attachUser($user)
	{
		if ($user instanceof GT_User)
		{
			$this->_user=$user;
			$this->_atts=null;
		}
		else
			throw new Exception('Error, trying to attach invalid user.');
	}
	public function loadGroups()
	{
		if (is_null($this->_groups))
			$this->_groups=(new GT_Users_Group_List($this->get('ID')))->getList();

	}
	public function getGroups()
	{
		if ($this->get('ID')==0)
			return array();

		$this->loadGroups();
		return $this->_groups;
	}
	public function belongs($user)
	{
		$rv=false;

		if (!($user instanceof GT_User))
			return false;

		if ($user->isSuper())
			return true;


		foreach($this->getGroups() as $group)
			if ($group->isMember($user))
				$rv=true;
		return $rv;
	}

	public function att($key)
	{
		$atts=$this->getAttsTree();
		return isset($atts[$key])?$atts[$key]['value']:'UNDEFINED';
	}
	public function getAtts()
	{
			return $this->getAttsTree();
	}
	public function getPermissionsTree($rv=array())
	{
		$app=new GT_App($this->get('appID'));

    $atts=(new GT_Permission_List(array($app->get('ID'), 'app'), array('objectID','objectType') ))->getList();
    foreach($atts as $att)
    	$rv[$att->get('slug')]=array('depth'=>1,'value'=>$att->get('active'),'id'=>$att->get('ID'), 'label'=>$att->get('name'), 'oid'=>$att->get('objectID'), 'type'=>$att->get('objectType'));

    $atts=(new GT_Permission_List(array($this->get('ID'), 'client'), array('objectID','objectType') ))->getList();
    foreach($atts as $att)
    	$rv[$att->get('slug')]=array('depth'=>2,'value'=>$att->get('active'),'id'=>$att->get('ID'), 'label'=>$att->get('name'), 'oid'=>$att->get('objectID'), 'type'=>$att->get('objectType'));
		return $rv;
	}
		public function createSMScode($uni){
		global $usersDB;
		$smsCode = substr(str_shuffle(str_repeat("23456789abcdefghjkmnpqrstuvwxyz", 5)), 0, 5);
		// ensure codes are unique
		while($usersDB->countResults("SELECT COUNT(code) FROM sms_temp_codes WHERE code='$smsCode'") > 0){$smsCode = substr(str_shuffle(str_repeat("23456789abcdefghjkmnpqrstuvwxyz", 5)), 0, 5);}
		$usersDB->insertStuff('sms_temp_codes',array('code'=>$smsCode,'created'=>date("Y-m-d H:i:s"),'uni'=>$uni),'users');
		return $smsCode;
	}
}
class GT_X_App_Client_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='appID')
	{
		$this->_db_table='x_apps_clients';
		$this->_fields=array('ID', 'appID', 'clientID', 'domain');
		$this->_class='GT_X_App_Client';

		parent::__construct($id, $by);
	}
}

?>

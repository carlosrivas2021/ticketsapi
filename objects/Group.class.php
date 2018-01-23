<?php
class GT_Users_Group extends T_Global_Tech_DB_Hierarchical_Object
{
	protected $_atts;
	protected $_permissions;
	protected $_users;
	protected $_atts_tree;
  function __construct($id=0, $by='ID',$row=array())
  {
		$this->_atts=null;
	  $this->_atts_tree=null;
		$this->_permissions=null;
		$this->_users=null;
    $this->_lister='GT_Users_Group_List';
    $this->_db_table='users_groups';
		$this->_meta_table='groups_meta';
		$this->_meta_idx='groupID';
    $this->_fields=array('ID', 'name', 'appClientID', 'parent');
    parent::__construct($id, $by, $row);
  }
	public function loadAtts($force=false)
	{
		if ($force||(is_null($this->_atts)))
			$this->_atts=(new GT_Attribute_List(array($this->get('ID'), 'group'), array('objectID','objectType') ));
	}
	public function loadPermissions($force=false)
	{
		if ($force||(is_null($this->_permissions)))
			$this->_permissions=(new GT_Permission_List(array($this->get('ID'), 'group'), array('objectID','objectType') ));
	}
	public function getPermissions()
	{
		$this->loadPermissions();
		return $this->_permissions->getList();
	}
	public function getAttsTree($rv=array())
	{
		if (is_null($this->_atts_tree))
		{
			$XAppClient= new GT_X_App_Client($this->get('appClientID'));
			$app=new GT_App($XAppClient->get('appID'));

			$rv=$app->getAttsTree($rv);
			$rv=$XAppClient->getAttsTree($rv);

			$branch=$this->getBranch();
			$branch=array_reverse($branch);
			foreach($branch as $grp)
			{
			$atts=(new GT_Attribute_List(array($grp->get('ID'), 'group'), array('objectID','objectType') ))->getList();
			foreach($atts as $att)
				$rv[$att->get('name')]=array('value'=>$att->get('value'),'id'=>$att->get('ID'));
			}
			$this->_atts_tree= $rv;
		}
		return $this->_atts_tree;
	}

	public function getPermissionsTree($rv=array())
	{
		$XAppClient= new GT_X_App_Client($this->get('appClientID'));
    $app=new GT_App($XAppClient->get('appID'));

    $rv=$app->getPermissionsTree($rv);
		$rv=$XAppClient->getPermissionsTree($rv);

		$depth=3;
    $branch=$this->getBranch();
    $branch=array_reverse($branch);
    foreach($branch as $grp)
    {
    	$atts=(new GT_Permission_List(array($grp->get('ID'), 'group'), array('objectID','objectType') ))->getList();
    	foreach($atts as $att)
    		$rv[$att->get('slug')]=array('depth'=>$depth, 'value'=>$att->get('active'),'id'=>$att->get('ID'), 'label'=>$att->get('name'), 'oid'=>$att->get('objectID'), 'type'=>$att->get('objectType'));
      $depth++;
    }
		foreach($rv as $k=>$v)
    	if (($v['value']=='0')&&($v['oid']!=$this->get('ID') || $v['type']!='group'))
      	unset($rv[$k]);

		return $rv;
	}

	public function att($key)
	{
		$atts=$this->getAttsTree();
		return isset($atts[$key])?$atts[$key]['value']:'UNDEFINED';
	}
	public function getAtts()
	{
		$this->loadAtts();
		return $this->_atts->getList();
	}

	public function getRoot()
	{
		if ($this->get('parent')==0)
			return $this;
		return (new GT_Users_Group($this->get('parent')))->getRoot();
	}

  function __clone()
  {
    $this->set('name', 'Copy of '.$this->get('name'));


    parent::__clone();

		foreach($this->getAtts() as $att)
		{
			$newAtt= clone $att;
      $newAtt->set('objectID',$this->get('ID'));
    	$newAtt->save();
		}
		foreach($this->getPermissions() as $perm)
		{
			$newPerm= clone $perm;
      $newPerm->set('objectID',$this->get('ID'));
    	$newPerm->save();
		}
  }
  public function delete($force=false)
  {
    if ((!$force)&&($this->hasUsers(true)))
        return false;

    return parent::delete();
  }
	public function isMember($user)
	{

		if (!($user instanceof GT_User))
			return false;

		$users=$this->getUsers();

		foreach($users as $u)
			if ($u->equals($user))
				return $this->get('ID');

		return false;

	}
	public function findOwnMember($user)
	{
		if (!($user instanceof GT_User))
			return false;
		$own=$this->getOwnUsers();
		foreach($own as $u)
			if ($u->equals($user))
				return $this->get('ID');
		return false;
	}
	public function getSiblings()
	{
		if ($this->get('parent')==0)
			return (new GT_Users_Group_List($this->get('appClientID')))->getList();
		else
			return (new GT_Users_Group($this->get('parent')))->getChildren();
	}
	public function findMember($user)
	{
		if (!($user instanceof GT_User))
			return false;
		$own=$this->findOwnMember($user);
		if (!$own)
		{
			foreach($this->getChildren() as $subgroup)
				$own=($own||$subgroup->findMember($user));

		} else
			return $own;

		return $own;

	}
	public function getUsers()
	{
		$own=$this->getOwnUsers();
		foreach($this->getChildren() as $subgroup)
		{
			foreach($subgroup->getUsers() as $u)
				$own[]=$u;
		}

		return $own;
	}
	public function getOwnUsers()
	{
		if (is_null($this->_users))
		{
			$this->_users=array();
			$tmp=(new GT_X_User_Client_List($this->get('ID'),'groupID'))->getList();
      $usersList=(new GT_User_List('1','active'))->getList();
      $UL=array();
      foreach($usersList as $k=>$ul)
        $UL[intval($ul->get('ID'))]=$usersList[$k];

			foreach($tmp as $t)
        if (isset($UL[intval($t->get('userID'))]))
				    $this->_users[]=$UL[intval($t->get('userID'))];
				// $this->_users[]=new GT_User($t->get('userID'));
		}
		return $this->_users;
	}
  public function hasUsers($checkChildren=true)
  {
    $users=$this->getOwnUsers();
    $own=(count($users)!=0);

    if ($checkChildren)
      foreach($this->getChildren() as $subgroup)
        $own=($own||$subgroup->hasUsers(true));

    return $own;
  }

}

class GT_Users_Group_List extends T_Global_Tech_DB_Object_List
{
	function __construct($id='', $by='appClientID')
	{

		$this->_db_table='users_groups';
    $this->_fields=array('ID', 'name', 'appClientID', 'parent');
		$this->_class='GT_Users_Group';

		parent::__construct($id, $by);
	}
}

<?php
class Logger
{
	protected static $db=null;

  public static function connect()
  {
    self::$db = new usersSql();
		@self::$db->connect(_AURORA_USERS_DATABASE, _AURORA_USERS, _AURORA_USERS_PASSWORD, 'logs');
  }
  public static function log($userID, $done_at, $ip, $domain, $path)
  {
    if (is_null(self::$db))
      self::connect();
    self::$db->insertStuff('user_activity',array('userID'=>$userID,'done_at'=>$done_at,'ip'=>$ip,'domain'=>$domain, 'path'=>$path),'logs');
  }
  public static function logNow($userID)
  {
    $domain=(isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'];
    $path=$_SERVER['REQUEST_URI'];
    self::log($userID, date('Y-m-d H:i:s'),$_SERVER['REMOTE_ADDR'] ,$domain, $path);
  }
	public static function getActivity($userID, $limit=3, $path='')
	{
		$domain=(isset($_SERVER['HTTPS']) ? "https" : "http") . "://".$_SERVER['HTTP_HOST'];
		$rv=array();
		if (is_null(self::$db))
      self::connect();
			$path=$path!=''?' AND path LIKE "%'.$path.'%" ':'';
			// $query='SELECT path, MAX(done_at) as done_at FROM user_activity WHERE userID='.$userID.' AND domain="'.$domain.'"'.$path.' GROUP BY path ORDER BY ID DESC LIMIT '.$limit;
			$query='SELECT DISTINCT path, done_at FROM user_activity WHERE userID='.$userID.' AND domain="'.$domain.'"'.$path.' ORDER BY ID DESC LIMIT '.$limit;
			// var_dump($query);
			// var_dump($query);
			$result=self::$db->query($query);
			while($row=self::$db->fetch_array($result))
				$rv[]=$row;
				return $rv;
	}

}

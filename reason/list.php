<?php
require_once '../config/config.php';
 $db = new QBuilder();
  $b = $db->select()
    ->from('reason')
    ->execute()
    ->result();
//var_dump($b);
$response['status']='success';
$response['msg']='Complete';
$response['data'] = $b;
die;

//$usersList=array();
//$users = (new GT_User_List())->getList();
//
//foreach($users as $user)
//{
//    $usersList[]=array
//    (
//        'ID'=>$user->get('ID'),
//        'first_name'=>$user->get('first_name'),
//        'last_name'=>$user->get('last_name')
//    );
//}
//var_dump($usersList);
//$response['status']='success';
//$response['msg']='Complete';
//$response['data']=$usersList;
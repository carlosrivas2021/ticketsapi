<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
include_once '../config/config.php';

$c= new Lists();
$b = $c->listing('GT_User_List');
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
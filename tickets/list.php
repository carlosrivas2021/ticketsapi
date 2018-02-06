<?php

require_once '../config/config.php';

class List_Ticket {

    public function listTicket($data) {
        if (isset($data["person_id"])) {
            $db = new QBuilder();
            $b = $db->select()
                    ->from('ticket')
                    ->where("person_id='" . $data["person_id"] . "'")
                    ->execute()
                    ->result();
            return $b;
        } 
        
        if (isset($data["id"])) {
            $db = new QBuilder();
            $b = $db->select()
                    ->from('ticket')
                    ->where("id='" . $data["id"] . "'")
                    ->execute()
                    ->result();
            return $b;
        }
        
        
            $db = new QBuilder();
            $b = $db->select()
                    ->from('ticket')
                    ->execute()
                    ->result();
            return $b;
        
    }

}

//var_dump($b);
$a = new List_Ticket();
$b = $a->listTicket($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
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
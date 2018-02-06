<?php
require_once '../config/config.php';


class List_Thread {

    public function listThread($data) {
        if (isset($data["ticket_id"])) {
            $db = new QBuilder();
            $b = $db->select()
                    ->from('thread')
                    ->where("ticket_id='" . $data["ticket_id"] . "'")
                    ->execute()
                    ->result();
            return $b;
        } else {
            $db = new QBuilder();
            $b = $db->select()
                    ->from('thread')
                    ->execute()
                    ->result();
            return $b;
        }
    }

}

//var_dump($b);
$a = new List_Thread();
$b = $a->listThread($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
die;
<?php

include_once '../config/config.php';

class Delete_Ticket {

    public function deleteTicket($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->delete("ticket")->where("id='".$data["id"]."'")
                    ->execute();

            if ($b) {
                return "ok";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }

}

//$data = array("id" => 19);
$a = new Delete_Ticket();
$b = $a->deleteTicket($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

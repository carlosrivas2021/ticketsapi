<?php

include_once '../config/config.php';

class Update_Ticket {

    public $ticketId = 0;

    public function updateTicket($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }
      
        if ($data) {
            $fecha = date("Y-m-d");
            $data["updated"] = $fecha;
            $this->ticketId = $data["id"];
            unset($data["id"]);
            $db = new QBuilder();
            $b = $db->update("ticket", $data)->where("id='" . $this->ticketId . "'")
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

//$data = array("id" => 19, "person_id" => "1", "status_id" => "1", "reason_id" => "1", "priority" => "High", "appClient_id" => "1", "title" => "New title update");
$a = new Update_Ticket();
$b = $a->updateTicket($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

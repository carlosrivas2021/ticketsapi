<?php

include_once '../config/config.php';

class Insert_Thread {

    public $ticketId;

    public function insertThread($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $fecha = date("Y-m-d");
            $data["created"]=$fecha;
            //$datos = array("ticket_id" => $this->ticketid, "event" => $this->event, "privated"=>$this->privated, "created" => $fecha);
            $db = new QBuilder();
            $b = $db->update("thread", $data)->where($_REQUEST["id"])
                    ->execute();

            if ($b) {
                $this->ticketId=$data['ticket_id'];
                $db->update("ticket", array("updated"=>$fecha))
                   ->where("id=$this->ticketId")
                   ->execute();
                
                return "ok";
            } else {
                return "error";
            }
        } else {
            return "error";
        }
    }

}

//$data = array("ticket_id" => "19", "event" => "Try again", "privated" => "1");
$a = new Insert_Thread();
$b = $a->insertThread($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

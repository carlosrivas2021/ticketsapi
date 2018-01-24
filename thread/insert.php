<?php

include_once '../config/config.php';

class Insert_Thread {

    public $privated;
    public $event;
    public $ticketId = 0;

    public function insertThread($data) {
        //var_dump($data);
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'privated':
                    $this->privated = $value;
                    break;
                case 'ticketid':
                    $this->ticketid = $value;
                    break;
                case 'event':
                    $this->event = $value;
                    break;
            }
        }

        if ($this->ticketid) {
            $fecha = date("Y-m-d");
            $datos = array("ticket_id" => $this->ticketid, "event" => $this->event, "created" => $fecha);
            $db = new QBuilder();
            $b = $db->insert("thread", $datos)
                    ->execute();

            if ($b) {
                $db->update("ticket", array("updated"=>$fecha))
                   ->where("id=$this->ticketid")
                   ->execute();
                
                return "ok";
            } else {
                return "error1";
            }
        } else {
            return "error2";
        }
    }

}

$data = array("ticketid" => "19", "event" => "Mucha practica...", "privated" => "1");
$a = new Insert_Thread();
$b = $a->insertThread($data);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

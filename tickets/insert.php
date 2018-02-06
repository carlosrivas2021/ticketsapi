<?php

include_once '../config/config.php';

class Insert_Ticket {

    public $userID;
    public $appClient;
    public $personId;
    public $reasonId;
    public $priority;
    public $title;
    public $event;
    public $ticketId = 0;

    public function insertTicket($data) {
        //var_dump($data);
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'userid':
                    $this->userID = $value;
                    break;
                case 'appClient_id':
                    $this->appClient = $value;
                    break;
                case 'person_id':
                    $this->personId = $value;
                    break;
                case 'title':
                    $this->title = $value;
                    break;
                case 'event':
                    $this->event = $value;
                    break;
                case 'priority':
                    $this->priority = $value;
                    break;
                case 'status_id':
                    $this->status = $value;
                    break;
                case 'reason_id':
                    $this->reason = $value;
                    break;
            }
        }
        $fecha = date("Y-m-d");
        $datos = array("person_id" => $this->personId, "status_id" => $this->status, "reason_id" => $this->reason, "priority" => $this->priority, "appClient_id" => $this->appClient, "title" => $this->title, "created"=> $fecha);
        $db = new QBuilder();
        $db->insert("ticket", $datos)
                ->execute();
        $this->ticketId = $db->insertId();
        if ($this->ticketId) {

            $datos = array("ticket_id" => $this->ticketId, "event" => $this->event, "created" => $fecha);
            $b = $db->insert("thread", $datos)
                    ->execute();
            if ($b) {
                return "ok";
            } else {
                return "error1";
            }
        } else {
            return "error2";
        }
    }

}

//$data = array("person_id" => "1", "appClient_id" => "1", "event" => "Mucha practica...", "priority" => "High", "reason_id" => "1", "status_id" => "1", "title" => "problema");
$a = new Insert_Ticket();
$b = $a->insertTicket($_REQUEST);

$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

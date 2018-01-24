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
         var_dump($data);
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'userid':
                    $this->userID = $value;
                    break;
                case 'appClient':
                    $this->appClient = $value;
                    break;
                case 'personId':
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
                case 'statusId':
                    $this->status = $value;
                    break;
                case 'reasonId':
                    $this->reason = $value;
                    break;
              
            }
        }

        $datos = array("person_id" => $this->personId, "status_id" => $this->status, "reason_id" => $this->reason, "priority" => $this->priority, "appClient_id" => $this->appClient, "title" => $this->title);
        $db = new QBuilder();
        $db->insert("ticket", $datos)
                ->execute();
        $this->ticketId = $db->insertId();
        if ($this->ticketId) {
            $datos = array("ticket_id" => $this->ticketId, "event" => $this->event, "created" => "now()");
            $b = $db->insert("thread", $datos)
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

$data = array("userid" => "1", "personId" => "1", "appClient" => "1", "event" => "Mucha practica...", "priority" => "High", "reasonId" => "1", "statusId" => "1", "title" => "problema");
$a = new Insert_Ticket();
$b = $a->insertTicket($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

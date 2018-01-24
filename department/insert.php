<?php

include_once '../config/config.php';

class Insert_Department {

    public $name;
    
    public function insertDepartment($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $fecha = date("Y-m-d");
            $data["created"] = $fecha;
            $db = new QBuilder();
            $this->name = $data["name"];
            $c = $db->select()
                    ->from('department')
                    ->where("name='$this->name'")
                    ->execute()
                    ->result();
            //var_dump($c);
            if ($c) {
                return "This name exist";
            } else {

                $b = $db->insert("department", $data)
                        ->execute();

                if ($b) {
                    return "ok";
                } else {
                    return "error";
                }
            }
        } else {
            return "error";
        }
    }

}

//$data = array("name" => "System3", "description" => "Responsible for solving system failures");
$a = new Insert_Department();
$b = $a->insertDepartment($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

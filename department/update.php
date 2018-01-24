<?php

include_once '../config/config.php';

class update_Department {

    public function updateDepartment($data) {
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
                    ->from('status')
                    ->where("name='$this->name'")
                    ->getRawQuery();

            if ($c) {
                return "This name exist";
            } else {

                $b = $db->update("department", $data)->where("id='".$data["id"]."'")
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

//$data = array("name" => "System", "description" => "Responsible for solving system failures");
$a = new update_Department();
$b = $a->updateDepartment($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

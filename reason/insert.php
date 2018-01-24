<?php

include_once '../config/config.php';

class Insert_Reason {

    public function insertReason($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->insert("reason", $data)
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

//$data = array("department_id" => 1,"title"=>"Sales", "description"=>"A new customer");
$a = new Insert_Reason();
$b = $a->insertReason($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

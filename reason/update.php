<?php

include_once '../config/config.php';

class update_Reason {

    public function updateReason($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->update("reason", $data)->where("id='".$_REQUEST["id"]."'")
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
$a = new update_Reason();
$b = $a->updateReason($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

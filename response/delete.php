<?php

include_once '../config/config.php';

class Delete_Response {

    public function deleteResponse($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->delete("response")->where("id='".$data["id"]."'")
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
$a = new Delete_Response();
$b = $a->deleteResponse($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

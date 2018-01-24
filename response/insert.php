<?php

include_once '../config/config.php';

class Insert_Response {

    public function insertResponse($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->insert("response", $data)
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

//$data = array("reason_id" => 1,"content"=>"This is a great history...!!!");
$a = new Insert_Response();
$b = $a->insertResponse($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

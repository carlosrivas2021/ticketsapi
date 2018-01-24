<?php

include_once '../config/config.php';

class update_Status {

    public $name;

    public function updateStatus($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {

            $db = new QBuilder();
            $this->name = $data["name"];
            $c = $db->select()
                    ->from('status')
                    ->where("name='$this->name'")
                    ->getRawQuery();
            
            if ($c) {
                return "This name exist";
            } else {



                $b = $db->update("status", $data)->where("id='".$data["id"]."'")
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

$data = $_REQUEST;
$a = new update_Status();
$b = $a->updateStatus($data);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

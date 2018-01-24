<?php

include_once '../config/config.php';

class Insert_Status {

    public $name;

    public function insertStatus($data) {
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
$a = new Insert_Status();
$b = $a->insertStatus($data);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

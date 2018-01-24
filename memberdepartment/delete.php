<?php

include_once '../config/config.php';

class Delete_Member_Department {

    public function deleteMemberDepartment($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $db = new QBuilder();
            $b = $db->delete("member_department")->where("id='".$data["id"]."'")
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
$a = new Delete_Member_Department();
$b = $a->deleteMemberDepartment($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

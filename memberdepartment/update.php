<?php

include_once '../config/config.php';

class Insert_Member_Department {

    public $department_id;
    public $user_id;

    public function insertMemberDepartment($data) {
        //var_dump($data);
        if (isset($data["key"])) {
            unset($data["key"]);
        }

        if ($data) {
            $this->department_id = $data["department_id"];
            $this->user_id = $data["user_id"];
            $db = new QBuilder();
            $c = $db->select()
                    ->from('member_department')
                    ->where("department_id=$this->department_id")
                    ->where("user_id=$this->user_id")
                    ->execute()
                    ->result();

            if ($c) {
                return "This user exist for this department";
            } else {

                $b = $db->update("member_department", $data)->where("id='".$data["id"]."'")
                        ->execute();
                //echo $b;
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

//$data = array("department_id" => 1, "user_id" => 3);
$a = new Insert_Member_Department();
$b = $a->insertMemberDepartment($_REQUEST);
//echo $b;
$response['status'] = 'success';
$response['msg'] = 'Complete';
$response['data'] = $b;
//
die;

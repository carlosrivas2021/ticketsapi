<?php
ini_set('display_errors',1);ini_set('display_startup_errors', 1);error_reporting(E_ALL);
require_once 'definitions.php';
require_once '../objects/sql.class.php';
require_once '../objects/QBuilder.php';
require_once '../objects/systemClasses.class.php';
require_once '../objects/User.class.php';
require_once '../objects/App.class.php';
require_once '../objects/Client.class.php';
require_once '../objects/Group.class.php';
require_once '../objects/Permission.class.php';
require_once '../objects/XAppClient.class.php';
require_once '../objects/XUserClient.class.php';
require_once '../objects/Attribute.class.php';
require_once '../objects/Logs.class.php';
require_once '../objects/Lists.class.php';

require_once '../objects/Role.class.php';
require_once '../objects/UserPassword.class.php';
require_once '../objects/XRolePermission.class.php';
require_once '../objects/XUserRole.class.php';


$usersDB = new usersSql();
$usersDBconn = $usersDB->connect(_AURORA_USERS_DATABASE, _AURORA_USERS, _AURORA_USERS_PASSWORD, 'users');

$crmDB = new usersSql();
$crmDBconn = $crmDB->connect(_TOURTRACK_USERS_DATABASE, _TOURTRACK_USERS, _TOURTRACK_USERS_PASSWORD, 'tourtrack');


//status = [initializing, error, success, unknown, incomplete]
$response = array('status'=>'initializing', 'msg'=>'Initializing');
function endpoint_shutdown()
{
    global $response;
    echo json_encode($response);
}
register_shutdown_function('endpoint_shutdown');

$auth=false;
//$_REQUEST['key']='VbNQU449RkJvDDE7Svq82Z1OikhNz6pl';
if (isset($_REQUEST['key']))
{
    try 
    { //validate key before passing it to GT_App class
 //       $app=new GT_App(trim($_REQUEST['key']), 'api_key');
        $app=new GT_App(trim($_REQUEST['key']), 'api_key');
        if ($app->get('ID')!='')
        $auth=true;
    } catch (Exception $ex)
    { }
}
if (!$auth)
{
    $response['status']='error';
    $response['msg']='Unauthorized';
    die;
}
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
//VbNQU449RkJvDDE7Svq82Z1OikhNz6pl
<?php
// --------------------------------------------------------------------
// wo_assign_workers.php -- Assign a worker to a work order.
//
// Created: 01/02/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();

$timer = new timer();
$error_msg = "";
$success_msg = "";
$userid = GetUserID();
$username = GetUserName();
$userIPT  = GetUserIPT($userid);
$pagetitle = "Assign Workers"; 
$dofrom = false;
$wid=0;

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["wid"])) DieWithMsg($loc, "No WID given.");
    $wid = $_GET["wid"];
    $wo = GetWO($wid);
    if(!$wo) 
    {
        $doform = false;
        $error_msg = "This Work Order doesn't seem to exist.";
        goto GenerateHtml;
    } 
    if(IsAdmin() || IsCaptain() || IsEditor() || IsIPTLead()) goto SetupForm;
    $success_msg = "You don't seem to have privilege to assign workers.";
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty($_POST["wid"])) DieWithMsg($loc, "No WID in POST.");
	$wid = intval($_POST["wid"]);
    $wo = GetWO($wid);
    if(!$wo) 
    {
        $doform = false;
        $error_msg = "This Work Order doesn't seem to exist.";
        goto GenerateHtml;
    } 
    $userinfo = GetUserInfo($userid);
    $username = MakeFullName($userinfo);  //MakeAbbrivatedName($userinfo);

    if(!empty($_POST["Add"])) 
    {
        if(empty($_POST["Workers"])) 
        {
            $error_msg = "No worker found. Cannot assign.";
            goto SetupForm;
        }
        $workername = $_POST["Workers"];
        $workerid = FindUser("FullName", $workername);
        $workerinfo = GetUserInfo($workerid);
        if(!$workerinfo)
        {
            $error_msg = "Worker not in database!  Cannot assign.";
            log_error($loc, array($error_msg, "Worker Name: " . $_POST["Workers"]));
            goto SetupForm;
        }
        MakeAssignment($wid, $workerid);
        $msg = 'New Assigment: "' . $workername . '" assigned by ' . $username;
        AttachSystemNote($wid, $msg);  
        goto SetupForm;
    }
    if(!empty($_POST["Remove"]))
    {
        if(empty($_POST["Workers"])) 
        {
            $error_msg = "No worker found. Cannot assign.";
            goto SetupForm;
        }
        $workername = $_POST["Workers"];
        $workerid = FindUser("FullName", $workername);

        $workerinfo = GetUserInfo($workerid);
        if(!$workerinfo)
        {
            $error_msg = "Worker not in database!  Cannot remove.";
            log_error($loc, array($error_msg, "Worker Name: " . $_POST["Workers"]));
            goto SetupForm;
        }
        RemoveAssignment($wid, $workerid);
        $msg = 'Deleted Assignment: "' . $workername . '" unassigned by ' . $username;
        AttachSystemNote($wid, $msg);  
        goto SetupForm;
    }
    DieWithMsg($loc, "Incorrect Post.");
}

SetupForm:
$pagetabtitle = "Epic " . $wo["WIDStr"];
$all_workers = GetAllWorkers();
$cur_workers = GetAssignedWorkers($wid);
$possible_workers = RemoveWorkers($all_workers, $cur_workers);
$possible_workers = SortForIPTTeam($possible_workers, $wo["Receiver"]);

$workers = array();
foreach($possible_workers as $w)
{
    $workers[] = $w["FirstName"] . ' ' . $w["LastName"];
}

$currentworkers = array();
foreach($cur_workers as $w)
{
    $currentworkers[] = $w["FirstName"] . ' ' . $w["LastName"];
}
$doform = true;


GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_head.css", "../css/wo_assign_workers.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_display_menubar.php";
include "forms/wo_assign_workers_form.php";  
include "forms/footer.php"; 

?>
<?php
// --------------------------------------------------------------------
// wo_change_status.php -- Changes the Status of a Work Order
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
$pagetitle = "Change Status of a Work Order"; 
$doform = false;
$wid=0;
$buttons = array();

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
    $pagetabtitle = "Epic " . $wo["WIDStr"];

	// Decide the sense of the buttons.
    $b_fin = "Finish";
    $b_apr = "Approve";
    $b_cpa = "Captain Approve";
    $b_cls = "Close";
    if($wo["Finished"])      $b_fin = "Unfinish";
    if($wo["Approved"])      $b_apr = "Unapprove";
    if($wo["ApprovedByCap"]) $b_cpa = "Captain Unapprove";
    if($wo["Closed"])        $b_cls = "Reopen";

    // Figure out if current user is assigned to this WO
    $isAssgined = false;
    $assigned_workers = GetAssignedWorkers($wid);
    foreach ($assigned_workers as $a) {
    	if($a["UserID"] == $userid) {$isAssgined = true; break;}
    }

    // Decide which buttons are allowed.
	$buttons = array();
    if(IsAdmin() || IsCaptain() || IsEditor() || IsIPTLead() || $isAssgined) $buttons[] = $b_fin;
    if(IsAdmin() || IsCaptain() || IsEditor() || IsIPTLead())                $buttons[] = $b_apr;
    if(IsAdmin() || IsCaptain())                                             $buttons[] = $b_cpa;
    if(IsAdmin() || IsCaptain() || IsEditor() || IsIPTLead())                $buttons[] = $b_cls;

    if(count($buttons) <= 0) 
    {
    	$doform = false;
    	$success_msg = "You don't seem to have privilege to make a status change.";
    	goto GenerateHtml;
    }
	$doform = true;
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty($_POST["wid"])) DieWithMsg($loc, "No WID in POST.");
	$wid = intval($_POST["wid"]);
    $wo = GetWO($wid);
    $pagetabtitle = "Epic " . $wo["WIDStr"];
    $userinfo = GetUserInfo($userid);
    $name = $userinfo["FirstName"] . ' ' . $userinfo["LastName"];
    $doform = true;

    $nc = 0;
	if(isset($_POST["Finish"]))
	{
		ChangeWOStatus($wid, $name, "Finished", true);
		$nc++;
	}
	if(isset($_POST["Approve"]))
	{
		if($wo["Revision"] <= 0) IncrementRevision($wid);
		ChangeWOStatus($wid, $name, "Approved", true);
		$nc++;
	}
	if(isset($_POST["Captain_Approve"]))
	{
		if($wo["Revision"] <= 0) IncrementRevision($wid);
		ChangeWOStatus($wid, $name, "ApprovedByCap", true);
		$nc++;
	}
	if(isset($_POST["Close"]))
	{
		ChangeWOStatus($wid, $name, "Closed", true);
		$nc++;
	}
	if(isset($_POST["Unfinish"]))
	{
		ChangeWOStatus($wid, $name, "Finished", false);
		$nc++;
	}
	if(isset($_POST["Unapprove"]))
	{
		ChangeWOStatus($wid, $name, "Approved", false);
		$nc++;
	}
	if(isset($_POST["Captain_Unapprove"]))
	{
		ChangeWOStatus($wid, $name, "ApprovedByCap", false);
		$nc++;
	}
	if(isset($_POST["Reopen"]))
	{
		ChangeWOStatus($wid, $name, "Closed", false);
		$nc++;
	}
	if($nc == 1) 
	{
		$wo = GetWO($wid);
		$pagetabtitle = "Epic " . $wo["WIDStr"];
		$success_msg = "Status Changed!";
		$doform = false;
		goto GenerateHtml;
	}
	DieWithMsg($loc, "Invalid number of status bits changed.  nc=" . $nc);

    goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_head.css", "../css/wo_change_status.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_display_menubar.php";
include "forms/wo_change_status_form.php";  
include "forms/footer.php"; 

?>
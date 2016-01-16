<?php
// --------------------------------------------------------------------
// utils_delete.php -- Page to delete a work order.
//
// Created: 01/07/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();

$timer = new timer();
$userid = GetUserID();
$userinfo = GetUserInfo($userid);
if($userinfo === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
$username = MakeFullName($userinfo);
$pagetitle = "Delete A Work Order";
$error_msg = "";
$success_msg = "";
$instructions = "";
$ins_file = "../docs/DeleteWorkOrder.md";
if(file_exists($ins_file)) { $instructions = MarkdownToHtml(file_get_contents($ins_file)); }
$doform = true;

$param_list = array(
array("FieldName" => "WID", "FieldType" => "Text", "Caption" => "Work Order ID"),
array("FieldName" => "Sure", "FieldType" => "Boolean", "Value" => false, "Caption" => "Are you Sure?"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	DenyGuest();
	PopulateParamList($param_list, $_POST);
	$wo = fixupwid($_POST["WID"]);
	if($wo === false) goto GenerateHtml;
	$sure = false;
	if(!empty($_POST["Sure"])) $sure = $_POST["Sure"];
	if(!$sure)
	{
		$error_msg = "Workorder not deleted. You didn't say that your sure.";
		goto GenerateHtml;
	}

	$success_msg = delete_workorder($wo);
	$doform = false;
	goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils_delete.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
include "forms/utils_delete_form.php";           
include "forms/footer.php"; 

// --------------------------------------------------------------------
function delete_workorder($woinfo)
{
	global $username;
	$loc = rmabs(__FILE__ . ".delete_workorder");
	$wid = $woinfo["WID"];
	$sql = "DELETE FROM Assignments WHERE WID=" . intval($wid);
	SqlQuery($loc, $sql);
	$sql = "DELETE FROM AppendedData WHERE WID=" . intval($wid);
	SqlQuery($loc, $sql);	
	$sql = "DELETE FROM WorkOrders WHERE WID=" . intval($wid);
	SqlQuery($loc, $sql);	
	$widstr = WIDStr($wid, $woinfo["Revision"], $woinfo["IsApproved"]);
	$msg =  "Work Order " . $widstr . " deleted by " . $username . ".";
	log_msg($loc, $msg);
	return $msg;
}

// --------------------------------------------------------------------
function FixUpWid($input)
{
	global $error_msg;
	if(empty($input)) {$error_msg = "Missing Input. Try again."; return false; }
	$wid = trim(strtoupper($input));
	if(strlen($wid) <= 0)
	{
		$error_msg = "Missing Input.  Try again.";
		return false;
	}
	if(substr($wid, 0, 1) == "W") $wid = substr($wid, 1);
	if(strlen($wid) <= 0)
	{
		$error_msg = "Missing Input.  Try again.";
		return false;
	}
	if(!checkdigits($wid))
	{
		$error_msg = "The input seems to be in the wrong format.  Use only numeric digits.";
		return false;
	}
	if($wid < 0 || $wid > 9999) 
	{
		$error_msg = "Input out of range.  Allowable range is from 0 to 9999.";
		return false;
	}
	$wo = GetWO(intval($wid));
	if(!$wo)
	{
		$error_msg = "Work Order W" . intval($wid) . ' not found.';
		return false;
	}
	return $wo;
}

// --------------------------------------------------------------------
function checkdigits($d)
{
	$digits = "0123456789";
	$n = strlen($d);
	for($i = 0; $i < $n; $i++)
	{
		$c = $d[$i];
		if(strpos($digits, $c) === false) return false;
	}
	return true;
}
?>
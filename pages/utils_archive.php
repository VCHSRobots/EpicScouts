<?php
// --------------------------------------------------------------------
// utils_archive.php -- Page to archive a work order.
//
// Created: 01/11/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
$userid = GetUserID();
$userinfo = GetUserInfo($userid);
if($userinfo === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
$username = MakeFullName($userinfo);

$timer = new timer();
$pagetitle = "Archive/Unarchive A Work Order";
$error_msg = "";
$success_msg = "";
$instructions = "";
$widarchivestr = "";
$widunarchivestr = "";
$ins_file = "../docs/Archive.md";
if(file_exists($ins_file)) { $instructions = MarkdownToHtml(file_get_contents($ins_file)); }
$doform = true;

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	DenyGuest();

	if(!empty($_POST["Archive"]))
	{
		$widarchivestr = $_POST["WIDArchive"];
		$wo = fixupwid($widarchivestr);
		if($wo === false) goto GenerateHtml;
		$success_msg = changeactive($wo["WID"], 0);
		log_msg($loc, $success_msg);
		$doform = false;
		goto GenerateHtml;
	}
	if(!empty($_POST["UnArchive"]))
	{
		$widunarchivestr = $_POST["WIDUnArchive"];
		$wo = fixupwid($widunarchivestr);
		if($wo === false) goto GenerateHtml;
		$success_msg = changeactive($wo["WID"], 1);
		log_msg($loc, $success_msg);
		$doform = false;
		goto GenerateHtml;		
	}
	DieWithMsg($loc, "Unknown post back.");
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils_archive.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
include "forms/utils_archive_form.php";           
include "forms/footer.php"; 

// --------------------------------------------------------------------
function changeactive($wid, $active)
{
	global $username;
	$loc = rmabs(__FILE__ . ".changeactive");
	$sql = 'UPDATE WorkOrders SET Active=' . intval($active) . ' WHERE WID=' . intval($wid);
	$result = SqlQuery($loc, $sql);
	$action = "archvied";
	if($active) $action = "resurrected";
	return 'Work Order ' . intval($wid) . ' has been ' . $action . ' by ' . $username . '.';
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
	$wo = GetWO(intval($wid), true);
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
<?php
// --------------------------------------------------------------------
// utils_merge.php -- Page to merge two work orders.
//
// Created: 01/07/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();

$timer = new timer();
$pagetitle = "Merge Two Work Orders Into One";
$error_msg = "";
$success_msg = "";
$instructions = "";
$visitbutton = "";
$ins_file = "../docs/MergeWorkOrder.md";
if(file_exists($ins_file)) { $instructions = MarkdownToHtml(file_get_contents($ins_file)); }
$doform = true;

$param_list = array(
array("FieldName" => "WID1", "FieldType" => "Text", "Caption" => "Primary Work Order ID"),
array("FieldName" => "WID2", "FieldType" => "Text", "Caption" => "Secondary Work Order ID"),
array("FieldName" => "Sure", "FieldType" => "Boolean", "Value" => false, "Caption" => "Are you Sure?"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	DenyGuest();
	PopulateParamList($param_list, $_POST);
	$wo1 = fixupwid($_POST["WID1"]);
	$wo2 = fixupwid($_POST["WID2"]);
	if($wo1 === false || $wo2 === false) goto GenerateHtml;
	$sure = false;
	if(!empty($_POST["Sure"])) $sure = $_POST["Sure"];
	if(!$sure)
	{
		$error_msg = "Workorders not merged. You didn't say that your sure.";
		goto GenerateHtml;
	}
	if($wo1["WID"] == $wo2["WID"])
	{
		$error_msg = "Workorders must be unique!";
		goto GenerateHtml;
	}

	if(empty($wo1) || empty($wo2)) goto GenerateHtml;

	$success_msg = merge_workorders($wo1, $wo2);
	$widstr = WIDstrHtml($wo1["WID"], $wo1["Revision"], $wo1["IsApproved"]);
	$visitbutton = '<a href="wo_display.php?wid=' . $wo1["WID"] . '"">Visit ' . $widstr . '</a>';
	$doform = false;
	goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils_merge.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
include "forms/utils_merge_form.php";           
include "forms/footer.php"; 

// --------------------------------------------------------------------
function merge_workorders($wo1, $wo2)
{
	$loc = rmabs(__FILE__ . ".merge_workorders");
	$wid1 = $wo1["WID"];
	$wid2 = $wo2["WID"];
	$wo1["Description"] .= "\n\n" . $wo2["Description"];
	UpdateWorkOrder($wid1, $wo1);
	$data = GetAppendedData($wid2);
	$nd = 0;
	foreach($data as $d)
	{
		if($d["UserID"] == 0) continue;  // Skip sys generated msg.
		if($d["Removed"]) continue;      // Skip deleted data.
		AppendWorkOrderData($wid1, $d["UserID"], $d["TextInfo"], $d["PicID"], false);
		$nd++;
	}
	$workers = GetAssignedWorkers($wid2);
	$nw = 0;
	foreach($workers as $w)
	{
		MakeAssignment($wid1, $w["UserID"]);
		RemoveAssignment($wid2, $w["UserID"]);
		$nw++;
	}
	$userid = GetUserID();
	$userinfo = GetUserInfo($userid);
	$username = MakeFullName($userinfo);
	if(!$wo2["Closed"])
	{
		ChangeWOStatus($wid2, $username, "Closed", true);
	}
	$newwostr = WIDStr($wid1, $wo1["Revision"], $wo1["IsApproved"]);
	AttachSystemNote($wid2, "This WO Merged into " . $newwostr . " by " . $username . '.');
	$oldwostr = WIDStr($wid2, $wo2["Revision"], $wo2["IsApproved"]);
	AttachSystemNote($wid1, "Data from " . $oldwostr . " merged into this one by " . $username . '.');
	$msg = 'Workorder ' . $oldwostr . ' merged into ' . $newwostr . '.  ';
	$msg .= 'Number Items Copied=' . $nd . '. ';
	$msg .= 'Number of Workers Reassigned=' . $nw . '. '; 
	log_msg($loc, (array($msg, "By " . $username)));
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
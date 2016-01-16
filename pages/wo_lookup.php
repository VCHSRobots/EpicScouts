<?php
// --------------------------------------------------------------------
// wo_lookup.php -- Page to allow Look Up of a Work Order.
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
$pagetitle = "Look Up A Work Order"; 
$wid="";

$param_list = array(
array("FieldName" => "WID", "FieldType" => "Text", "Caption" => "Work Order ID"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{

    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	$wid = $_POST["WID"];
	$wid = trim(strtoupper($wid));
	if(strlen($wid) <= 0)
	{
		$error_msg = "Nothing Input.  Try again.";
		goto GenerateHtml;
	}
	if(substr($wid, 0, 1) == "W") $wid = substr($wid, 1);
	if(strlen($wid) <= 0)
	{
		$error_msg = "Nothing Input.  Try again.";
		goto GenerateHtml;
	}
	if(!checkdigits($wid))
	{
		$error_msg = "The input seems to be in the wrong format.  Use only numeric digits.";
		$d["WID"] = $wid;
		PopulateParamList($param_list, $d);
		goto GenerateHtml;
	}
	if($wid < 0 || $wid > 9999) 
	{
		$error_msg = "Input out of range.  Allowable range is from 0 to 9999.";
		$d["WID"] = $wid;
		PopulateParamList($param_list, $d);
		goto GenerateHtml;
	}
	$wo = GetWO(intval($wid), true);
	if(!$wo)
	{
		$widstr = sprintf("W%04d", intval($wid));
		$error_msg = "Work Order " . $widstr . ' not found.';
		goto GenerateHtml;
	}
	if($wo["Active"] == 0)
	{
		$widstr = sprintf("W%04d", intval($wid));
		$error_msg = "Work Order " . $widstr . ' has been archived.  It must be resurrected by a captain before it can be viewed.';
		goto GenerateHtml;
	}

	$d = array();
	$d["wid"] = intval($wid);
	JumpToPage("pages/wo_display.php", $d);
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_lookup.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_lookup_form.php";           
include "forms/footer.php"; 

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
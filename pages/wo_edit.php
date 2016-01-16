<?php
// --------------------------------------------------------------------
// wo_edit.php -- Edits on a work order. 
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
$pagetitle = "Edit Work Order"; 
$wid="";
$doform = false;

$param_list = array(
array("FieldName" => "WID",        "FieldType" => "Hidden"),
array("FieldName" => "Title",      "FieldType" => "Text",                                    "Caption" => "Title of New Work Order"),
array("FieldName" => "Project",    "FieldType" => "Selection", "Selection" => $WOProjects,   "Caption" => "Project"),
array("FieldName" => "DateNeedBy", "FieldType" => "Date",                                    "Caption" => "Date Needed"),
array("FieldName" => "Priority",   "FieldType" => "Selection", "Selection" => $WOPriorities, "Caption" => "Priority"),
array("FieldName" => "Requestor",  "FieldType" => "Selection", "Selection" => $WOIPTeams,    "Caption" => "Requesting IPT"),
array("FieldName" => "Receiver",   "FieldType" => "Selection", "Selection" => $WOIPTeams,    "Caption" => "Receiving IPT"),
array("FieldName" => "Description","FieldType" => "TextArea", "Rows" => 10, "Columns" => 72, "Caption" => "Describe Work"));

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

    $IsAuthor = false;
    if($wo["AuthorID"] == $userid) $IsAuthor = true;
    if($wo["IsApproved"]) $IsAuthor = false;   // Don't allow authors to changed approved WOs.

    if(!IsAdmin() && !IsCaptain() && !IsEditor() && !IsIPTLead() && !$IsAuthor)
    {
    	$success_msg = "You do not seem to have privilege to edit this work order.";
    	$doform = false;
    	goto GenerateHtml;
    }

    PopulateParamList($param_list, $wo);
    $pagetabtitle = "Epic " . $wo["WIDStr"];
    $doform = true;
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
	if(empty($_POST["WID"])) DieWithMsg($loc, "No WID in POST.");
	$wid = intval($_POST["WID"]);
    $wo = GetWO($wid);

    PopulateParamList($param_list, $_POST);

    // Check for required inputs:
    $sEmpty = array();
    if(empty($_POST["Title"]))       $sEmpty[] = "Title";
    if(empty($_POST["Priority"]))    $sEmpty[] = "Priority";
    if(empty($_POST["Project"]))     $sEmpty[] = "Project";
    if(empty($_POST["Requestor"]))   $sEmpty[] = "Requesting IPT";
    if(empty($_POST["Receiver"]))    $sEmpty[] = "Receiving IPT";
    if(empty($_POST["Description"])) $sEmpty[] = "Description of Work";
    if(empty($_POST["DateNeedBy"]))  $sEmpty[] =" Date Needed";
    if(count($sEmpty) > 0)
    {
        $error_msg = "Required information missing: ";
        $c = 0;
        foreach($sEmpty as $s) 
        {
            if($c > 0) $error_msg .= ', ';
            $error_msg .= $s;
            $c++;
        }
        $error_msg .= '.';
        $doform = true;
        goto GenerateHtml;
    }

    $dt = $_POST["DateNeedBy"];
    unset($dgood);
    $d1 = date_parse_from_format("Y-m-d", $dt); if(empty($d1["errors"])) $dgood = $d1; 
    $d2 = date_parse_from_format("m/d/Y", $dt); if(empty($d2["errors"])) $dgood = $d2;
    $d3 = date_parse_from_format("y-m-d", $dt); if(empty($d3["errors"])) $dgood = $d3;
    $d4 = date_parse_from_format("m/d/y", $dt); if(empty($d4["errors"])) $dgood = $d4;

    if(empty($dgood))
    {
        $error_msg = "Bad Date input.  Use yyyy-mm-dd or mm/dd/yy.";
        goto GenerateHtml;
    }
    $dtt = sprintf("%04d-%02d-%02d", $dgood["year"], $dgood["month"], $dgood["day"]);

    $data = ExtractValuesFromParamList($param_list);
    $date["DateNeededBy"] = $dtt;

    $error_msg = UpdateWorkOrder($wid, $data);
    if($error_msg)
    {
    	$doform = true;
    	goto GenerateHtml;
    }
    $userinfo = GetUserInfo($userid);
    $username = MakeFullName($userinfo);
    AttachSystemNote($wid, "Workorder edited by ". $username);
    IncrementRevision($wid);
    $success_msg = "Workorder updated! The revision has been increased.";
    $wo = GetWO($wid);
    $doform = false;
    goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_head.css", "../css/wo_new.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_display_menubar.php";
include "forms/wo_edit_form.php";                // !!! Change this when feature is ready.
include "forms/footer.php"; 

?>
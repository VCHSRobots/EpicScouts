<?php
// --------------------------------------------------------------------
// wo_new.php -- Adds a new work order. 
//
// Created: 12/31/15 DLB
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
$doform = false;
$link_to_view = false;
$picid = 0;

$param_list = array(
array("FieldName" => "Title",      "FieldType" => "Text",                                    "Caption" => "Title of New Work Order"),
array("FieldName" => "Project",    "FieldType" => "Selection", "Selection" => $WOProjects,   "Caption" => "Project"),
array("FieldName" => "DateNeedBy", "FieldType" => "Date",                                    "Caption" => "Date Needed"),
array("FieldName" => "Priority",   "FieldType" => "Selection", "Selection" => $WOPriorities, "Caption" => "Priority"),
array("FieldName" => "Requestor",  "FieldType" => "Selection", "Selection" => $WOIPTeams,    "Caption" => "Requesting IPT"),
array("FieldName" => "Receiver",   "FieldType" => "Selection", "Selection" => $WOIPTeams,    "Caption" => "Receiving IPT"),
array("FieldName" => "Description","FieldType" => "TextArea", "Rows" => 10, "Columns" => 72, "Caption" => "Describe Work"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{

    // Set up defaults...
    $data["Priority"]   =  $WOPriorities[0];
    $data["Requestor"]  =  $userIPT;
    $data["Receiver"]   =  $userIPT;
    $data["DateNeedBy"] =  date('Y-m-d', time() + (5 * 24 * 3600));
    
    PopulateParamList($param_list, $data);
    $doform = true;
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    DenyGuest();
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
    
    // !!! Check to make sure the title is unique.

    // Check to make sure the date is okay.
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

    // Add some important stuff
    $data["Revision"] = 0;
    $data["AuthorID"] = $userid;
    $data["DateNeedBy"] = $dtt;
    $data["DateCreated"] = date('Y-m-d');
    $data["Assigned"] = false;
    $data["Approved"] = false;
    $data["ApprovedByCap"] = false;
    $data["Finished"] = false;
    $data["Closed"] = false;
    $data["Active"] = true;

    $r = CreateNewWorkOrder($data);
    if($r[1] !== true)
    {
        $error_msg = $r[1];
        $doform = true;
        goto GenerateHtml;
    }
    $wid = $r[0];

    PopulateParamList($param_list, $data);
    $success_msg = "New work order " . WIDStrHtml($wid, 0, false) . " created!";
    $link_to_view = "wo_display.php?wid=" . $wid;
    $doform = false; 
}

GenerateHtml:
$picid = GetPicIDForUserID($userid);
if($picid > 0) 
{
    $picurl = PicUrl($picid, "thumb");
}

$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_new.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_new_form.php";
include "forms/footer.php";

?>
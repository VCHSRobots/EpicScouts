<?php
// --------------------------------------------------------------------
// wo_display.php -- Main Place to Display a Work Order  
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
$pagetitle = "Work Order"; 
$doform = true;
$wid="";
$ap=array();
$assigned_workers=array();
$primarypic_url = "";
$primarypic_ref = "";
$override = false;

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["wid"])) DieWithMsg($loc, "No WID given.");
    $wid = $_GET["wid"];
    if(isset($_GET["override"])) $override = $_GET["override"];
    $wo = GetWO($wid, $override);
    if(!$wo) 
    {
        $doform = false;
        $error_msg = "This Work Order doesn't seem to exist.";
        goto GenerateHtml;
    } 


    $pagetabtitle = "Epic " . $wo["WIDStr"];
    $pagetitle = "Work Order";
    $ap = GetAppendedData($wid);
    $assigned_workers = GetAssignedWorkers($wid);
    $wo["Description"] = wordwrap($wo["Description"], 65, "\n", true);

    $picinfo = GetPrimaryPicInfo($wid);
    if($picinfo)
    {
        $picid = $picinfo["PicID"];
        $primarypic = PicPathName($picid, "tiny");
        if(file_exists($primarypic)) 
        {
            $primarypic_url = PicUrl($picid, "tiny");
            $primarypic_ref = 'display_image.php?picid=' . $picid . '&wid=' . $wid;
        }
    }
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_head.css", "../css/wo_display.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_display_menubar.php";
include "forms/wo_display_form.php";
include "forms/footer.php";

?>
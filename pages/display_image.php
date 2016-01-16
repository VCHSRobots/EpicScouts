<?php
// --------------------------------------------------------------------
// display_image.php -- Display an image associated with a Work Order  
//
// Created: 01/03/16 DLB
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
$wid = 0;
$picid = 0;
$picurl = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["wid"])) DieWithMsg($loc, "No WID given.");
    if(empty($_GET["picid"])) DieWithMsg($loc, "No picid given.");
    $wid = intval($_GET["wid"]);
    $wo = GetWO($wid);
    $picid = intval($_GET["picid"]);
    $pagetabtitle = "Epic " . $wo["WIDStr"];
    $pagetitle = "Picture for Work Order";
    $piccaption = GetPicCaption($wid, $picid);
    if($picid > 0) {
        $picfile = PicPathName($picid, "orig");
        if(file_exists($picfile)) {
            $picurl = PicUrl($picid, "orig");
        }
    }
    if(empty($picurl)) $error_msg = "Unable to find Pic... Sorry.";
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    $error_msg = "You should not be able to access this page in this way.";
    goto GenerateHtml;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/wo_head.css", "../css/display_image.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_display_menubar.php";
include "forms/display_image_form.php";
include "forms/footer.php";


// --------------------------------------------------------------------
// Gets the info for the caption on the pic.
function GetPicCaption($wid, $picid)
{
    $loc = rmabs(__FILE__ . ".GetPicCaption");
    if(empty($wid) || empty($picid)) return "";
    $sql = 'SELECT * From AppendedData Where WID=' . intval($wid) . ' AND PicID=' . intval($picid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows <= 0) return "";
    $row = $result->fetch_assoc();
    return $row["TextInfo"];
}
?>
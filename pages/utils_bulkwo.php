<?php
// --------------------------------------------------------------------
// utils_bulkwo.php -- Generate Bulk WOs.
//
// Created: 01/06/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$pagetitle = "Generate Bulk Work Orders";
$error_msg = "";
$success_msg = "";
$instructions = "";
$ins_file = "../docs/BulkWOInstructions.md";
if(file_exists($ins_file)) { $instructions = MarkdownToHtml(file_get_contents($ins_file)); }

$doform = true;

$sql = 'SELECT * FROM Users ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);
$names = array();
$names[] = "[System]";
while($row = $result->fetch_assoc())
{
    $names[] = $row["LastName"] . ', ' . $row["FirstName"];
}

$param_list = array(
array("FieldName" => "Title",      "FieldType" => "Text",                                    "Caption" => "Title of New Work Order"),
array("FieldName" => "Project",    "FieldType" => "Selection", "Selection" => $WOProjects,   "Caption" => "Project"),
array("FieldName" => "DateNeedBy", "FieldType" => "Date",                                    "Caption" => "Date Needed"),
array("FieldName" => "Priority",   "FieldType" => "Selection", "Selection" => $WOPriorities, "Caption" => "Priority"),
array("FieldName" => "Requestor",  "FieldType" => "Selection", "Selection" => $WOIPTeams,    "Caption" => "Requestor IPT"),
array("FieldName" => "Author",     "FieldType" => "Selection", "Selection" => $names),
array("FieldName" => "FilterTags", "FieldType" => "Text", "Caption" => "Filter Tags (Blank for all workers)"),
array("FieldName" => "Description","FieldType" => "TextArea", "Rows" => 10, "Columns" => 72, "Caption" => "Describe Work"));

if( $_SERVER["REQUEST_METHOD"] == "GET")
{

    // Set up defaults...
    $data["Priority"]   =  $WOPriorities[0];
    $data["DateNeedBy"] =  date('Y-m-d', time() + (3 * 24 * 3600));
    
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

    // Make sure title has an ## for replace.
    $title = $_POST["Title"];
    if(stripos($title, "##") === false)
    {
        $error_msg = "You must include a ## in the title which will be replace by a count.";
        $doform = true;
        goto GenerateHtml;
    }

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

    // Figure out who is to be used as author.
    $authorid = 0;
    if($_POST["Author"] != "[System]")
    {
        $authorid =  FindUser("LastNameFirst", $_POST["Author"]);
        if(!$authorid)
        {
            $error_msg = "Unable to find author id! ";
            $doform = true;
            goto GenerateHtml; 
        }
    }

    // Fix up unspecified parameters.
    $_POST["DateNeedBy"] = $dtt;
    $_POST["Revision"] = 1;    // Since already pre-approved, start as 1.
    $_POST["AuthorID"] = $authorid;  
    $_POST["DateCreated"] = date("Y-m-d");
    $_POST["Assigned"] = 1;
    $_POST["Approved"] = 1;
    $_POST["ApprovedByCap"] = 0;
    $_POST["Finished"] = 0;
    $_POST["Closed"] = 0;
    $_POST["Active"] = 1;
    $success_msg = GenerateBulkWO($_POST);
    $doform = false;
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils_bulkwo.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
include "forms/utils_bulkwo_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function GenerateBulkWO($params)
{
    global $WOIPTeams;
    $loc = rmabs(__FILE__ . "GenerateBulkWO");

    $sql = 'SELECT * FROM AllActiveUsersView ORDER BY LastName, FirstName';
    $result = SqlQuery($loc, $sql);
    $d = array();
    $num = 0;
    $matchtags = ArrayFromSlashStr($params["FilterTags"]);
    if(empty($matchtags)) $matchtags = array("Worker");

    $title_template = $params["Title"];
    $nerr = 0;
    $nok = 0;
    $wid0 = 0;
    $wid1 = 0;
    $num = 0;
    while($userinfo = $result->fetch_assoc()) 
    {
        // Decide if this person should get a WO.
        $taglist = ArrayFromSlashStr($userinfo["Tags"]);
        if(TagMatch(array("Guest"), $taglist)) continue;       // Guests NEVER get one.
        if(!TagMatch($matchtags, $taglist)) continue;

        // We passed the test, this person gets one!
        // Figure out the receiving IPT.
        $ipt = $userinfo["IPT"];
        if(empty($ipt)) $ipt = $WOIPTeams[8];  // Hopefully this is management.
        $params["Receiver"] = $ipt;

        $num++;
        $snum = sprintf("%d", $num);
        $params["Title"] = TemplateReplace($title_template, $snum, "##");
        $rwo = CreateNewWorkOrder($params);
        $wid = $rwo[0];
        if($wid == 0) {
            // failed.
            log_error($loc, array("Failed to Create Bulk WO. Reason: " . $rwo[1], 'WO Title: ' . $params["Title"]));
            $nerr++;
            continue;
        }
        // Add assingment
        MakeAssignment($wid, $userinfo["UserID"]);
        if($nok == 0) $wid0 = $wid;
        $wid1 = $wid;
        $nok++;
    }
    $msg = 'Number of WOs Created = ' . $nok . '.  Number of Failures = ' . $nerr . '.';
    $msg .= "  WID=" . $wid0 . " to " . $wid1 . ".";
    log_msg($loc, "Bulk WO Created. WIDs ". $wid0 . " to " . $wid1);
    return $msg;
}

// --------------------------------------------------------------------
// Returns true only if all tags in $matchtag are found in $taglist.
function TagMatch($matchtags, $taglist)
{
    foreach($matchtags as $m)
    {
        $found = false;
        foreach($taglist as $t)
        {
            if(strtolower(trim($m)) == strtolower(trim($t))) {$found=true; break;}
        }
        if(!$found) return false;
    }
    return true;
}

// --------------------------------------------------------------------
function GetAllWorkers2()
{
    $loc = rmabs(__FILE__ . ".get_all_accounts");
    $sql = "SELECT UserID from Users WHERE Active=1";
    $result = SqlQuery($loc, $sql);
    while($row = $result->fetch_assoc())
    {
        $aws[] = intval($row["UserID"]);
    }
    return $aws;
}



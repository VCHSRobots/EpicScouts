<?php
// --------------------------------------------------------------------
// wo_sort_advanced.php -- page to filter results more specifically
// Created: 1/1/16 SS
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();


$tableheader = "";
$tabledata = "";
$sql = "";


if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
	
    if($_POST["DateNeededStart"] > $_POST["DateNeededEnd"])
    {
    	$error_msg = "Invalid Need By Date. Need By End must be after Need By Start.";
    	$_SESSION["ERROR"] = $error_msg;
    	JumpToPage("pages/wo_sort_advanced.php");
    }
    else if($_POST["DateCreatedStart"] > $_POST["DateCreatedEnd"])
    {
    	$error_msg = "Invalid DateCreated. Created End must be after Created Start.";
    	$_SESSION["ERROR"] = $error_msg;
    	JumpToPage("pages/wo_sort_advanced.php");
    }
    else unset($_SESSION["ERROR"]);
    /*store input in SESSION so it can still be accessed after redirected to GET */
    $_SESSION["FILTERS"] = $_POST;
    JumpToPage("pages/wo_sort_advanced.php");
}



if( $_SERVER["REQUEST_METHOD"] == "GET")
{

    if(isset($_SESSION["FILTERS"]))
    {
        $view  = $_SESSION["FILTERS"]["View"];
        $priority   = $_SESSION["FILTERS"]["Priority"];
        $receiver = $_SESSION["FILTERS"]["ReceivingTeam"];
        $title = $_SESSION["FILTERS"]["Title"];
        $requestor  = $_SESSION["FILTERS"]["RequestingTeam"];
        $author   = $_SESSION["FILTERS"]["Author"];
        $project = $_SESSION["FILTERS"]["Project"];
        $studentAssigned = $_SESSION["FILTERS"]["StudentAssigned"];
        $createdStart  = $_SESSION["FILTERS"]["DateCreatedStart"];
        $createdEnd   = $_SESSION["FILTERS"]["DateCreatedEnd"];
        $needByStart = $_SESSION["FILTERS"]["DateNeededStart"];
        $needByEnd = $_SESSION["FILTERS"]["DateNeededEnd"];
        $approved  = $_SESSION["FILTERS"]["Approved"];
        $approvedByCap = $_SESSION["FILTERS"]["ApprovedByCap"];
        $assigned = $_SESSION["FILTERS"]["Assigned"];
        $finished = $_SESSION["FILTERS"]["Finished"];
        $closed = $_SESSION["FILTERS"]["Closed"];
        $filters = $_SESSION["FILTERS"];



    }
	else{
        /*initialize variables to empty*/
    	$title = $requestor = $author = $project = $studentAssigned = $createdStart = 
    		$createdEnd = $needByStart = $needByEnd = $approved = $approvedByCap = $assigned = 
    	    $finished = $closed = $view = $priority = $receiver = "";

    	/*place form data into array to be passed to CreateFilterSQL()*/
    	$filters = array("Title" => $title, "Author" => $author, "StudentAssigned"=> $studentAssigned,
    		"DateCreatedStart"=>$createdStart,"DateCreatedEnd"=>$createdEnd, "DateNeededStart"=>$needByStart,
    		"DateNeededEnd"=>$needByEnd,"RequestingTeam"=>$requestor, "View" => $view, "Priority" => $priority,
    		"ReceivingTeam" => $receiver, "Project" => $project,"Approved"=>$approved,
    		"ApprovedByCap"=>$approvedByCap, "Assigned"=>$assigned,"Finished"=>$finished, "Closed"=>$closed);
    }

    if($studentAssigned !=""){
            $sql = "SELECT FirstName, LastName FROM Users WHERE UserID = " . $studentAssigned;
            $result = SqlQuery($loc, $sql);
            if($row = $result->fetch_assoc()){
                $assignName = $row["FirstName"] . " " .  $row["LastName"];
            }
        }
        if($author !=""){
        $sql = "SELECT FirstName, LastName FROM Users WHERE UserID = " . $author;
        $result = SqlQuery($loc, $sql);
        if($row = $result->fetch_assoc()){
            $authorName = $row["FirstName"] . " " . $row["LastName"];
        }
    }

    if(isset($_SESSION["ERROR"]))
    {
        $error_msg = $_SESSION["ERROR"];
        $sql = "SELECT * FROM ActiveWorkOrders";
    }
    else $sql = CreateFilterSQL($filters);  /* lib function that returns filtering SQL Query */

    //print $sql; /*used for testing*/
    $filterresult= SqlQuery($loc, $sql);
    $pagetitle = "";
    $pagetext = "";
    if($sql != "")
    {
        if($view == "full") $tableheader = array("WO", "Title", "Receiving IPT", "DateNeeded", "CAP", "AP", "AS" , "F ", "C");
        else     $tableheader = array("WO", "Title", "Receiving IPT", "DateNeeded");
        $tabledata = array();
        while($row = $filterresult->fetch_assoc())
        {
            $wid = $row["WID"];
            $rev = $row["Revision"];
            $app = ($row["Approved"] || $row["ApprovedByCap"]);
            $woname = WIDStrHtml($wid, $rev, $app);
            unset($dd);
            $dd=array();
            $dd[] = '<a href="wo_display.php?wid=' . $row["WID"] . '">' . $woname . '</a>';
            $dd[] = $row["Title"];
            $dd[] = $row["Receiver"];
            $dd[] = $row["DateNeedBy"];
            if($view =="full")
            {
                if($row["ApprovedByCap"]) $dd[] = "X";
                else $dd[] = "--";
                if($app) $dd[] = "X";
                else $dd[] = "--";
                if($row["Assigned"]) $dd[] = "X";
                else $dd[] = "--";
                if($row["Finished"]) $dd[] = "X";
                else $dd[] = "--";        
                if($row["Closed"]) $dd[] = "X";
                else $dd[] = "--";
            }
            $tabledata[] = $dd;
        }
    }
}

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/findlist.css", "../css/statuskey.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/findlist_menubar.php";
include "forms/findlist_advanced_form.php";
include "forms/findlist_data_form.php";
echo '</div>';
include "forms/footer.php";

?>
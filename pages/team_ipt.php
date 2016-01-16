<?php
// --------------------------------------------------------------------
// team_ipt.php -- Page to show various IPT listings for team view. 
//
// Created: 01/04/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

session_start();
log_page();
CheckLogin();
$pagetitle = "Team View Listing";
$nlimit = 100;
$limittext = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    if(empty($_GET["teamid"])) DieWithMsg($loc, "No teamid given.");
    if(empty($_GET["searchtype"])) DieWithMsg($loc, "No searchtype given.");
    $teamid = intval($_GET["teamid"]);
	if($teamid <= 0 || $teamid >= count($WOIPTeams)) DieWithMsg($loc, "Bad teamid given.");
	$teamname = $WOIPTeams[$teamid];
	$searchtype = strtolower($_GET["searchtype"]);

	$sql = 'SELECT * from ActiveWorkOrders WHERE Receiver="' . $teamname . '" AND ';
	if($searchtype == 'open') 
	{	
		$sql .= 'Closed = 0';
		$searchtitle = "All Opened Work Orders";
	}
	else if($searchtype == "approved") 
	{
		$sql .= 'Closed=0 AND (Approved=1 OR ApprovedByCap=1)';
		$searchtitle = "All Opened and Approved Work Orders";
	}
	else if($searchtype == "unapproved") 
	{
		$sql .= 'Closed=0 AND (Approved=0 AND  ApprovedByCap=0)';
		$searchtitle = "All Opened and UnApproved Work Orders";
	}
	else if($searchtype == "urgent") 
	{
		$sql .= 'Closed=0 AND Finished=0 AND Priority="Urgent"';
		$searchtitle = "All Opened, Unfinished and Urgent Work Orders";
	}
	else if($searchtype == "finished") 
	{
		$sql .= 'Closed=0 AND Finished=1';
		$searchtitle = "All Opened and Finished Work Orders";
	}
	else if($searchtype == "closed") 
	{
		$sql .= 'Closed=1';
		$searchtitle = "All Closed Work Orders";
	}
	else 
	{
		DieWithMsg($loc, "Unknown searchtype given.");
	}

	$tableheader = array("WO", "Title", "Pri", "Need BY", "Asgnd", "Aprv", "Fin", "Clsd");
	$sql .= ' Limit ' . $nlimit;
	$result = SqlQuery($loc, $sql);
	$tabledata = array();
	$ncount = 0;
	while($row = $result->fetch_assoc())
	{
		$isapproved = $row["Approved"] || $row["ApprovedByCap"];
		$wid = $row["WID"];
		$widstr = WIDStr($wid, $row["Revision"], $isapproved);
		$r = array();
		$r[] = '<a href="wo_display.php?wid=' . $wid .'">' . $widstr . '</a>';
		$r[] = LimitSize($row["Title"], 24);
		$r[] = $row["Priority"];
		$r[] = $row["DateNeedBy"];
		$r[] = YesNoStr($row["Assigned"]);
		$r[] = YesNoStr($isapproved);
		$r[] = YesNoStr($row["Finished"]);
		$r[] = YesNoStr($row["Closed"]);
		$tabledata[] = $r;
		$ncount++;
	}
	if($ncount >= $nlimit) $limittext = "Note: Output limited to " . $nlimit . " records.";
}

$stylesheet=array("../css/global.css", "../css/nav.css", "../css/team_ipt.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/team_menubar.php";
include "forms/team_ipt_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function YesNoStr($thing)
{
	if($thing) return "Y";
	else return " ";
}

?>

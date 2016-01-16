<?php
// --------------------------------------------------------------------
// inbox.php -- page where IPTLeads can see all work for their teams
//
// Created: 12/31/15 DLB
// Updated: 1/4/16 SS -- filled in Inbox pages
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

session_start();
log_page();
CheckLogin();

$tableheader = "";
$tabledata = "";
$sql = "";
$userid = GetUserID();
$isuser = false;
$nlimit = 100;
$limittext = "";

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
	// Decide on what IPT to use...
	if(!empty($_GET["UseSelfTeam"]))
	{
		// The requestor wants us to be sure to use our own team.
		unset($_SESSION["InBoxTeam"]);  // Next time use us again.
		$IPT = GetUserIPT($userid);
	}
	else if(!empty($_GET["teamid"]))
	{
		// Team ID was given.  Respect it.
		$teamid = intval($_GET["teamid"]);
		if($teamid <= 0 || $teamid > count($WOIPTeams)) DieWithMsg($loc, "Bad team id given in URL.");
		$IPT = $WOIPTeams[$teamid];
		$_SESSION["InBoxTeam"] = $IPT;	// Save it away for next time.
	} 
	else
	{
		if(isset($_SESSION["InBoxTeam"]))
		{
			$IPT = $_SESSION["InBoxTeam"];
		}
		else
		{
			$IPT = getUserIPT($userid);
		}
	}
	// We now have the IPT team we are going to use.  If it matches the user's IPT
	// team, the messaging is changes somewhat.
	if($IPT == GetUserIPT($userid)) $isuser = true;
	$iptsearch = $IPT;
	if(empty($iptsearch)) $iptsearch = "WILL NOT MATCH";

	if(!empty($_GET["Assigned"]))
	{
		$pagetitle = "Assigned Work Orders";
		if($isuser) $pagetext = "These are work orders for your team that you have already assigned.";
		else        $pagetext = "These are work orders that have already been assigned.";
		$tabledata = array();

		$sql = 'Select * FROM ActiveWorkOrders WHERE Receiver = "' . $iptsearch . '" AND Assigned = 1 AND Closed = 0 Limit ' . $nlimit;
		$result = SqlQuery($loc, $sql);
		if ($result->num_rows <= 0) 
		{
			if($isuser) $pagetext = "There are no work orders that you have already assigned.  When there are, they will be listed here.";
			else "There are no work orders already assigned for this team.";
			goto GenerateHtml;
		}
	}

	if(!empty($_GET["Unassigned"]))
	{
		$pagetitle = "UnAssigned Work Orders";
		if($isuser) $pagetext = "These are work orders for your team that you have not assigned to someone.";
		else        $pagetext = "These are work orders that have not been assigned.";
		$sql = 'Select * FROM ActiveWorkOrders WHERE Receiver = "' . $iptsearch . '" AND Assigned = 0 AND Closed = 0 Limit ' . $nlimit;
		$result = SqlQuery($loc, $sql);
		if ($result->num_rows <= 0) 
		{
			if($isuser) $pagetext = "There are no work orders that you have not assigned.  When there are, they will be listed here.";
			else $pagetext = "There are no work orders for this team that have not been assigned.";
			goto GenerateHtml;
		}
	}
	if(!empty($_GET["Completed"]))
	{
		$pagetitle = "Completed Work Orders";
		if($isuser) $pagetext = "These are work orders that your team has completed.";
		else        $pagetext = "These are work orders that this team has completed.";
		$tabledata = array();

		$sql = 'Select * FROM ActiveWorkOrders WHERE Receiver = "' . $iptsearch . '" AND Finished = 1 AND Closed = 0 Limit ' . $nlimit;
		$result = SqlQuery($loc, $sql);
		if ($result->num_rows <= 0) 
		{
			if($isuser) $pagetext = "There are no work orders that your team has completed.  When there are, they will be listed here.";
			else        $pagetext = "There are no work orders completed by this team.";
			goto GenerateHtml; 
		}
	}

	if(!empty($_GET["Opened"]))
	{
		$pagetitle = "Open Work Orders" ;
		if($isuser) $pagetext = "These are work orders for your team that are still open.";
		else        $pagetext = "These are work orders for this team that are still open.";
		$sql = 'Select * FROM ActiveWorkOrders WHERE Receiver = "' . $iptsearch . '" AND Closed = 0 Limit ' . $nlimit;
		$result = SqlQuery($loc, $sql);
		if ($result->num_rows <= 0) 
		{
			if($isuser) $pagetext = "There are no open work orders for your team.  When there are, they will be listed here.";
			else        $pagetext = "There are no open work orders for this team.";
			goto GenerateHtml;
		}
	}
	if(!empty($_GET["Closed"]))
	{
		$pagetitle = "Closed Work Orders";
		if($isuser) $pagetext = "These are work orders for your team that have already been closed.";
		else        $pagetext = "These are work orders for this team that have already been closed.";
		$sql = 'Select * FROM ActiveWorkOrders WHERE Receiver = "' . $iptsearch . '" AND Closed = 1 Limit ' . $nlimit;
		$result = SqlQuery($loc, $sql);
		if ($result->num_rows <= 0) 
		{
			if($isuser) $pagetext = "There are no closed work orders for your team.  When there are, they will be listed here.";
			else        $pagetext = "There are no closed work orders for this team.";
			goto GenerateHtml;
		}
	}

	if($sql != ""){
		if(empty($_GET["Closed"]))	$tableheader = array("WO", "Title", "Date Needed", "CAP", "AP", "AS" , "F ");
		else $tableheader = array("WO", "Title", "Date Needed", "CAP", "AP", "AS" , "F ", "C");
		$tabledata = array();
		$ncount = 0;
		while($row = $result->fetch_assoc()) {
			$wid = $row["WID"];
			$rev = $row["Revision"];
			$app = ($row["Approved"] || $row["ApprovedByCap"]);
			$woname = WIDStrHtml($wid, $rev, $app);
			unset($dd);
			$dd=array();
			$dd[] = '<a href="wo_display.php?wid=' . $row["WID"] . '">' . $woname . '</a>';
			$dd[] = LimitSize($row["Title"], 24);
			$dd[] = $row["DateNeedBy"];
			if($row["ApprovedByCap"]) $dd[] = "X";
			else $dd[] = "--";
			if($app) $dd[] = "X";
			else $dd[] = "--";
			if($row["Assigned"]) $dd[] = "X";
			else $dd[] = "--";
			if($row["Finished"]) $dd[] = "X";
			else $dd[] = "--";
			if(!empty($_GET["Closed"]))
			{			
				if($row["Closed"]) $dd[] = "X";
				else $dd[] = "--";
			}
			$tabledata[] = $dd;
			$ncount++;
		}
		if($ncount >= $nlimit) $limittext = "Note: Output limited to " . $nlimit  . " records.";
	goto GenerateHtml;
	}

	$pagetitle = "In Box";
	$pagetext = "<p>Here, you can manange the work that has been assigned to your team.</p>";
	$pagetext .= "<p>Use the links above to get started.";
	goto GenerateHtml;

    $data = GetUserInfo($userid);
    if($data === false) DieWithMsg($loc, 'User with ID=' . $userid . ' not found.');
    
    PopulateParamList($param_list, $data);
    goto GenerateHtml;
}

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
}

GenerateHtml:


$stylesheet=array("../css/global.css", "../css/nav.css", "../css/inbox.css", "../css/statuskey.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/inbox_menubar.php";
include "forms/inbox_form.php";
include "forms/footer.php";

?>

<?php
// --------------------------------------------------------------------
// team.php -- Team Views to show overall status.
//
// Created: 01/04/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

session_start();
log_page();
CheckLogin();

$pagetitle = "Overview Of All Work Orders";

$sql = "SELECT * FROM ActiveWorkOrders";
$result = SqlQuery($loc, $sql);
$allwo = array();
while($row = $result->fetch_assoc()) { $allwo[] = $row; }

$sql = "SELECT * FROM Assignments";
$result = SqlQuery($loc, $sql);
$allasgn = array();
while($row = $result->fetch_assoc()) { $allasgn[] = $row; }

// Now, with all the data, loop through all the IPT Teams
$bigtable = array();
$teamid = 0;
foreach($WOIPTeams as $t)
{
	if(empty($t)) {$teamid++; continue; }
	$bigtable[] = DoCals($t, $teamid, $allwo, $allasgn);
	$teamid++;
}

// Decide about inbox ability
$show_inbox_links = false;
if(IsAdmin() || IsEditor() || IsIPTLead() || ISCaptain() ) $show_inbox_links = true;

// Final stats 
$nWO = count($allwo);
$nOpened = 0;
foreach($bigtable as $row) {$nOpened += $row["Opened"]; }
$nClosed = 0;
foreach($bigtable as $row) {$nClosed += $row["Closed"]; }
$nUrgent = 0;
foreach($bigtable as $row) {$nUrgent += $row["Urgent"]; }

// Prepare the actual output table.
$tableheader = array("IP Team", "Open", "UnAprvd", "Aprvd", "Urgent", "Finished", "Wrkrs", "Closed");
$tabledata = array();
foreach($bigtable as $t)
{
	$ref = '<a href=team_ipt.php?teamid=' . $t["TeamID"] . '&searchtype=';
	$r = array();
	if($show_inbox_links) $r[] = '<a href=inbox.php?Opened=Yes&teamid=' . $t["TeamID"] . '>' . $t["IP Team"] . '</a>';
	else                  $r[] = $t["IP Team"];
	$r[] = $ref . 'open>'     . $t["Opened"]    . '</a>';
	$r[] = $ref . 'unapproved>' . $t["UnApproved"]  . '</a>';
	$r[] = $ref . 'approved>' . $t["Approved"]  . '</a>';
	$r[] = $ref . 'urgent>'   . $t["Urgent"]    . '</a>';
	$r[] = $ref . 'finished>' . $t["Finished"]  . '</a>';
	$r[] = $t["Workers"];
	$r[] = $ref . 'closed>'   . $t["Closed"]    . '</a>';
	$tabledata[] = $r;
}

GenerateHTML:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/team.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/team_menubar.php";
include "forms/team_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
// Compiles all the stats for the given team.
function DoCals($team, $teamid, $allwo, $allasgn)
{
	$Total = 0;
	$Opened = 0;
	$UnApproved = 0;
	$Approved = 0;
	$Urgent = 0;
	$Normal = 0;
	$High = 0;
	$Low = 0;
	$Closed = 0;
	$Finished = 0;
	$Assigned = 0;
	$workers = array();
	foreach($allwo as $w)
	{
		if($w["Receiver"] != $team) continue;
		$Total++;
		if($w["Closed"]) $Closed++;
		else
		{
			$Opened++;
			if($w["Assigned"]) $Assigned++;
			if($w["Approved"] || $w["ApprovedByCap"]) $Approved++;
			if(!$w["Approved"] && !$w["ApprovedByCap"]) $UnApproved++;
			if($w["Finished"]) $Finished++;
			else 
			{
				if($w["Priority"] == "Urgent") $Urgent++;
				if($w["Priority"] == "Normal") $Normal++;
				if($w["Priority"] == "High")   $High++;
				if($w["Priority"] == "Low")    $Low++;				
			}
		}
		AddUniqueWorkers($w["WID"], $allasgn, $workers);
	}
	$a = array();
	$a["IP Team"]  = $team;
	$a["TeamID"]   = $teamid;
	$a["Total"]    = $Total;
	$a["Opened"]   = $Opened;
	$a["Closed"]   = $Closed;
	$a["Approved"] = $Approved;
	$a["UnApproved"] = $UnApproved;
	$a["Finished"] = $Finished;
	$a["Assigned"] = $Assigned;
	$a["Urgent"]   = $Urgent;
	$a["Normal"]   = $Normal;
	$a["High"]     = $High;
	$a["Low"]      = $Low;
	$a["Workers"]  = count($workers);
	return $a;
}

// --------------------------------------------------------------------
// Keeps a running list of unique workers.
function AddUniqueWorkers($wid, $allasgn, &$workers)
{
	// build an array of workers for this $wid.
	$w = array();
	foreach($allasgn as $a)
	{
		if($a["WID"] != $wid) continue;
		$w[] = $a["UserID"];
	}

	foreach($w as $id)
	{
		if(!in_array($id, $workers)) $workers[] = $id;
	}
}



?>

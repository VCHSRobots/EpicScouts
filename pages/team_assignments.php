<?php
// --------------------------------------------------------------------
// team_assignments.php -- Shows WO Assignemnts for entire team.
//
// Created: 01/05/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();
session_start();
log_page();
CheckLogin();

$pagetitle = "Work Order Assignments";

$sql = 'SELECT * FROM UserView WHERE Active=1 ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);
$workers = array();
while($row = $result->fetch_assoc()) 
{
	if(CheckRawTagList("Worker", $row["Tags"]))
	{
		$workers[] = $row;
	}
}

$sql = 'SELECT * FROM AssignmentsView WHERE Closed=0';
$result = SqlQuery($loc, $sql);
$allasignments = array();
while($row = $result->fetch_assoc()) 
{
	$allasignments[] = $row;
}

$table = array();
foreach($workers as $w)
{
	$table[] = BuildRow($w, $allasignments);
}

GenerateHTML:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/team_assignments.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/team_menubar.php";
include "forms/team_assignments_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function BuildRow($worker, $allasignments)
{
	$row = array();
	$row["Name"] = $worker["LastName"] . ', ' . $worker["FirstName"];
	$list = array();
	foreach($allasignments as $a)
	{
		if($a["UserID"] == $worker["UserID"]) $list[] = $a["WID"];
	}
	$row["Data"] = $list;
	return $row;
}


?>
<?php
// --------------------------------------------------------------------
// team_org.php -- Shows IPT teams.
//
// Created: 01/04/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

session_start();
log_page();
CheckLogin();

$pagetitle = "Team Organization";

$sql = 'SELECT * FROM UserView WHERE Active=1 ORDER BY FirstName, LastName';
$result = SqlQuery($loc, $sql);
$everybody = array();
while($row = $result->fetch_assoc()) 
{
	$everybody[] = $row;
}

$teams = array();
$tm = array();
$tm["Name"] = "Captains";
$tm["Members"] = GetTaggedPepole("captain", $everybody);
$coaches = GetTaggedPepole("headcoach", $everybody);
foreach($coaches as $c)
{
	$tm["Members"][] = $c . ' - Head Coach';
}

$teams[] = $tm;

foreach($WOIPTeams as $t)
{
	if(empty($t)) continue;
	$tm = array();
	$tm["Name"] = $t;
	$tm["Members"] = GetTeamMembers($t, $everybody);
	$teams[] = $tm;
}

$tm = array();
$tm["Name"] = "Mentors";
$tm["Members"] = GetTaggedPepole("mentor", $everybody);
$teams[] = $tm;

$tm = array();
$tm["Name"] = "[Not Assigned]";
$tm["Members"] = GetTeamMembers("", $everybody, true);
if(count($tm["Members"]) > 0) $teams[] = $tm;

GenerateHTML:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/team_org.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/team_menubar.php";
include "forms/team_org_form.php";
include "forms/footer.php";

// --------------------------------------------------------------------
function GetTaggedPepole($tag, $everybody)
{
	$list = array();
	foreach($everybody as $person)
	{
		if(CheckRawTagList($tag, $person["Tags"])) 
		{
			$list[] = $person["FirstName"] . ' ' . $person["LastName"];
		}
	}
	return $list;
}

// --------------------------------------------------------------------
function GetTeamMembers($team, $everybody, $workers=false)
{
	$list = array();
	$leadlist = array();
	$workerlist = array();
	$mentorlist = array();

	foreach($everybody as $p)
	{
		if(CheckRawTagList("guest", $p["Tags"])) continue;
		if($workers)
		{
			if(CheckRawTagList("worker", $p["Tags"]) === false) continue;
			if(CheckRawTagList("mentor", $p["Tags"])) continue;
		}
		if($p["IPT"] == $team) 
		{
			$m = $p["FirstName"] . ' ' . $p["LastName"];
			if(CheckRawTagList("iptlead", $p["Tags"]))
			{
				 $m .= ' - Lead';
				$leadlist[] = $m;
			}
			else if(CheckRawTagList("mentor", $p["Tags"]))
			{
				 $m .= ' - Mentor';
				$mentorlist[] = $m;
			}
			else $workerlist[] = $m;
		}
	}
	$list = array_merge($leadlist, $workerlist, $mentorlist);
	return $list;
}

?>

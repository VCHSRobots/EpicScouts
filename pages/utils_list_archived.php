<?php
// --------------------------------------------------------------------
// utils_list_archived.php -- Page to list archvied WOs.
//
// Created: 01/11/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
$timer = new timer();
$pagetitle = "List of Archvied Work Orders";
$pagetext = "";
$tableheader = array("WO", "Title", "Created", "CAP", "AP", "AS", "F", "C");
$tabledata = "";
$nlimit = 500;
$limittext = "";

$sql = "";
$sql = 'Select * from WorkOrders WHERE Active=0';
$result = SqlQuery($loc, $sql);
$tabledata = array();
$ncount = 0;
while($row = $result->fetch_assoc()) {
	$wid = $row["WID"];
	$rev = $row["Revision"];
	$app = ($row["Approved"] || $row["ApprovedByCap"]);
	$woname = WIDStrHtml($wid, $rev, $app);
	unset($dd);
	$dd=array();
	$dd[] = '<a href="wo_display.php?override=1&wid=' . $row["WID"] . '">' . $woname . '</a>';
	$dd[] = LimitSize($row["Title"], 24);
	$dd[] = $row["DateCreated"];
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
	$tabledata[] = $dd;
	$ncount++;
}
if($ncount >= $nlimit) $limittext = 'Note: Output limited to ' . $nlimit . ' records.';

GenerateHtml:
$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils_list_archived.css", "../css/statuskey.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
include "forms/utils_list_archived_form.php";
include "forms/footer.php";

?>
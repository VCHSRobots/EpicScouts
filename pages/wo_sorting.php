<?php
// --------------------------------------------------------------------
// wo_sorting.php -- page to show work orders
//
// Created: 12/31/15 SS
// --------------------------------------------------------------------

require_once "../maindef.php";
session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$loc = 'wo_sorting.php';
$error_msg = "";

if(isset($_SESSION["ShowListParams"]))
{
    $view      = $_SESSION["ShowListParams"]["View"];
    $priority   = $_SESSION["ShowListParams"]["Priority"];
    $status  = $_SESSION["ShowListParams"]["Status"];
    $iptgroup = $_SESSION["ShowListParams"]["IPTGroup"];
    $project = $_SESSION["ShowListParams"]["Project"];
}
else{
    $view = "simple";
    $iptgroup = "all";
}

if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    $view   = isset($_POST["View"]);
    $priority  = isset($_POST["Priority"]);
    $status = isset($_POST["Status"]);
    $iptgroup = isset($_POST["IPTGroup"]);
    $project = intval($_POST["Project"]);
}

$IPTGroupArray = array("all");
$LeadIPTGroup = getIPTGroup($loc);
if($LeadIPTGroup != false)
{
	array_push($IPTGroupArray, $LeadIPTGroup);
}
$IPTGroups = getIPTGroups($loc);
$IPTGroupArray = array_merge($IPTGroupArray, $IPTGroups);
$Projects = getAllProjects($loc);

/*
$param_list = array(
array("FieldName" => "View", "FieldType" => "Selection", "Selection" => array("Simple", "Full")),
array("FieldName" => "Priority", "FieldType" => "Selection", "Selection" => array("High", "Normal", "Low")),
array("FieldName" => "IPT Group", "FieldType" => "Selection", "Selection" => $IPTGroupArray),
array("FieldName" => "Status", "FieldType" => "Selection", "Selection" => array("Unapproved", "Unassigned", "Assigned", "Completed", "Closed")),
);
*/

if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    goto GenerateHtml;
}


// Store settings away, so not lost if user leaves the page...
$t = array("View" => $view, "Priority" => $priority, "Status" => $status,
     "IPTGroup" => $iptgroup, "Project" => $project);
$_SESSION["ShowListParams"] = $t;

unset($output_lines);

if($data === false) { $error_msg = "Invalid date."; }
else if(empty($data)) { $error_msg = "Log file not found."; }
else
{
    $output_lines = FilterListData($view, $priority, $iptgroup, $status, $project);
}

$output_lines = ReverseLogLines($output_lines);


GenerateHtml:
include "forms/header.php";
include "forms/nav_form.php";
//include "forms/admin_menubar.php";
include "forms/wo_sorting_form.php";
//include "forms/admin_log_data.php";
echo '</div>';
include "forms/footer.php";

?>
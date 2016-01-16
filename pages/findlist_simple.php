
<?php
// ------------------------------------------------------------------------
// findlist_simple.php -- Main page to list work orders in all their glory.
//      This form lets you select basic criteria to filter WOs by.
//      the filter options are: view (full or simple), ReceivingIPT, and 
//      Priority.
//
// Created: 12/31/15 SS
// ------------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
$timer = new timer();
log_page();
CheckLogin();

$tableheader = "";
$tabledata = "";
$sql = "";
$results = "";
$isResult = 0;
$nlimit = 100;
$pagetext = "";
$limittext = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    /*set default values for these fields incase the user switches to advanced form */
    $_POST["Title"] = "";
    $_POST['RequestingTeam'] = "";
    $_POST['Author'] = "";
    $_POST['Project'] = "";
    $_POST['StudentAssigned'] = "";
    $_POST['DateCreatedStart'] = "";
    $_POST['DateCreatedEnd'] = "";
    $_POST['DateNeededStart'] = "";
    $_POST["DateNeededEnd"] = "";
    $_POST['Approved'] = "";
    $_POST['ApprovedByCap'] = "";
    $_POST['Assigned'] = "";
    $_POST['Finished'] = "";
    $_POST['Closed'] = "";

	/*store input in SESSION so it can still be accessed after redirected to GET */
	$_SESSION["FILTERS"] = $_POST;
    /*this allows users to return to page via 'back' */
    JumpToPage("pages/findlist_simple.php");
}


if( $_SERVER["REQUEST_METHOD"] == "GET")
{
    /*access values stored in session */
    if(isset($_SESSION["FILTERS"]))
    {
        $view  = $_SESSION["FILTERS"]["View"];
        $priority   = $_SESSION["FILTERS"]["Priority"];
        $receiver = $_SESSION["FILTERS"]["ReceivingTeam"];
        $filters = $_SESSION["FILTERS"];
    }
    /*set default values if no session data */
    else
    {
        $view = "simple";
        $priority = "";
        $receiver = "";
        $filters = array("View" => "simple", "Priority" => "","ReceivingTeam" => "");
    }
    /* lib function that returns filtering SQL Query */
    $sql = CreateFilterSQL($filters, $nlimit);	
    $result = SqlQuery($loc, $sql);
	
    $pagetitle = "";
    $pagetext = "";
    if($sql != "")
    {
        /*set tableheader */
        if($view == "full") $tableheader = array("WO", "Title", "Receiving IPT", "DateNeeded", "CAP", "AP", "AS" , "F ", "C");
        else     $tableheader = array("WO", "Title", "Receiving IPT", "DateNeeded");
        /*set table data */
        $tabledata = array();
        $ncount = 0;
        while($row = $result->fetch_assoc())
        {
			$isResult = 1;
            $wid = $row["WID"];
            $rev = $row["Revision"];
            $app = ($row["Approved"] || $row["ApprovedByCap"]);
            $woname = WIDStrHtml($wid, $rev, $app);
            unset($dd);
            $dd=array();
            $dd[] = '<a href="wo_display.php?wid=' . $row["WID"] . '">' . $woname . '</a>';
            $dd[] = LimitSize($row["Title"], 24);
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
            $ncount++;

        }
		/*check if there were any results */
		if(!$isResult)
		{
			$pagetext = "<br><p>There are no existing Work Orders for these search parameters.</p>";
			goto GenerateHtml;
		}
        if($ncount >= $nlimit) $limittext = "Note: Output limited to " . $nlimit . " records.";
    }


}




GenerateHtml:

$stylesheet=array("../css/global.css", "../css/nav.css", "../css/findlist.css", "../css/statuskey.css");
include "forms/header.php";
include "forms/nav_form.php";
include "forms/findlist_menubar.php";
include "forms/findlist_simple_form.php";
include "forms/findlist_data_form.php";
echo '</div>';
include "forms/footer.php";

?>
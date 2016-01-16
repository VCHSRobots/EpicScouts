<?php
// --------------------------------------------------------------------
// wo_setworkder.php -- 
//
// Created: ??
// --------------------------------------------------------------------

session_start();
require_once '../maindef.php';
$loc = rmabs(__FILE__);


//JumpToPage("workorders_thisuser.php");
if(isset($_POST['userid']))
{
//	&&
//   $_POST['completed'] == 'Yes')
//{
	log_msg($loc,
       array("User assigned!  for Work Order ID =" . $_SESSION["WorkOrderID"]));
	$WorkOrderID = $_SESSION["WorkOrderID"];
	$userid = $_SESSION["UserID"];
        $success_msg = 'Work Order  ' . $WorkOrderID . ' completed.';
	$sql =  'UPDATE WorkOrders SET AssignedTo ="' . $userid . '"  WHERE WorkOrderID = "' . $WorkOrderID . '";';
	$result = SqlQuery($loc, $sql);
	echo "Worker  " . $_SESSION["WorkOrderID"] . " Completed";
    	JumpToPage("workorders_thisuser.php");
}


?>

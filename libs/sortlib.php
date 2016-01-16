<?php
// --------------------------------------------------------------------
// sortlib.php:  library fucntions that deal with sorting and finding.
//
// Created: 1/01/16 SS
// --------------------------------------------------------------------

// This code contains functions used in the find/list pages.
// They are used for filtering and sorting the work orders to obtain
// the desired search results.

// Public API
// ==========
// CreateFilterSQL() -- returns a SQL query based on the passed array.
// ---------------------------------------------------------------------  

// ------ --------------------------------------------------------------
// Filters work orders based on the specified criteria
//  Keys that can be included in $filters:
//			-"StudentAssigned": UserID of student assigned to a WO
//			-"Title": Title of the WO
//			-"Author": User who made the WO
//			-"Priority": one of the $WOPriorities
//			-"DateCreatedStart": starting end of a date range
//			-"DateCreatedEnd": closing end of a date range
//			-"DateNeededStart": starting end of a date range
//			-"DateNeededEnd": closing end of a date range
//			-"RequestingTeam": IPT team requesting the work order
//			-"ReceivingTeam": IPT team receiving the work order
//			-"Project": work order project name
//			-"Assigned": 0 or 1
//			-"Approved": 0 or 1
//			-"ApprovedByCap": 0 or 1
//			-"Finished": 0 or 1
//			-"Closed": 0 or 1
//			-"Active": 0 or 1
//	Each of these keys can be left empty (or all of them). If no key
// 	pairs are entered, the function will return a SQL query that has
//  no limits on it.
// --------------------------------------------------------------------
function CreateFilterSQL($filters, $nlimit = 0)
{
	$multipleWheres = false;

	$sql = "SELECT ";

		$sql .= " ActiveWorkOrders.WID, Title, Receiver, Requestor, Project, Priority, DateNeedBy, Approved, Assigned, ApprovedByCap, Finished, Closed, Active, Revision";

	$sql .=" FROM ActiveWorkOrders ";

	if(isset($filters["StudentAssigned"]) && $filters["StudentAssigned"]!="")
	{
		$sql .= " JOIN Assignments ON ActiveWorkOrders.WID = Assignments.WID ";
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "Assignments.UserID = " . $filters["StudentAssigned"];
	}
	if(isset($filters["Title"]) && $filters["Title"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= " Title = '" . $filters["Title"] . "'";
	}
	if(isset($filters["Author"]) && $filters["Author"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= " AuthorID = " . $filters["Author"] ;
	}
	if(isset($filters["Priority"]) && $filters["Priority"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= " Priority = '" . $filters["Priority"] ."'" ;	
	}
	if(isset($filters["DateCreatedStart"]) && isset($filters["DateCreatedEnd"]) && $filters["DateCreatedStart"]!="" && $filters["DateCreatedEnd"]!="" &&$filters["DateCreatedStart"]<= $filters["DateCreatedEnd"])
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "(DateCreated BETWEEN '" . $filters["DateCreatedStart"] . "' AND '" . $filters["DateCreatedEnd"] . "')";
	}
	if(isset($filters["DateNeededEnd"]) && isset($filters["DateNeededStart"]) && $filters["DateNeededStart"]!="" && $filters["DateNeededEnd"]!="" &&$filters["DateNeededStart"]<= $filters["DateNeededEnd"])
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "(DateNeedBy BETWEEN '" . $filters["DateNeededStart"] . "' AND '" . $filters["DateNeededEnd"] . "')";
	}
	if(isset($filters["RequestingTeam"]) && $filters["RequestingTeam"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "Requestor = '" . $filters["RequestingTeam"] . "'";
	}
	if(isset($filters["ReceivingTeam"]) && $filters["ReceivingTeam"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "Receiver = '" . $filters["ReceivingTeam"] . "'";
	}
	if(isset($filters["Project"]) && $filters["Project"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		$sql .= "Project = '" . $filters["Project"] . "'";
	}
	if(isset($filters["Assigned"]) && $filters["Assigned"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		if($filters["Assigned"] == 0) $sql .= " Assigned = 0 ";
		else if($filters["Assigned"]==1) $sql .= " Assigned = 1 ";
	}
	if(isset($filters["Approved"]) && $filters["Approved"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		if($filters["Approved"] == 0) $sql .= " Approved = 0 ";
		else if($filters["Approved"]==1) $sql .= " Approved = 1 ";
	}
	if(isset($filters["ApprovedByCap"]) && $filters["ApprovedByCap"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		if($filters["ApprovedByCap"] == 0) $sql .= " ApprovedByCap = 0 ";
		else if($filters["ApprovedByCap"]==1) $sql .= " ApprovedByCap = 1 ";
	}
	if(isset($filters["Finished"]) && $filters["Finished"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		if($filters["Finished"] == 0) $sql .= " Finished = 0 ";
		else if($filters["Finished"]==1) $sql .= " Finished = 1 ";
	}
	if(isset($filters["Closed"]) && $filters["Closed"]!="")
	{
		$sql .= CheckMultipleWheres($multipleWheres);
		$multipleWheres = true;
		if($filters["Closed"] == 0) $sql .= " Closed = 0 ";
		else if($filters["Closed"]==1) $sql .= " Closed = 1 ";
	}
	//Remove following because Active must be true in ActiveWorkOrders.
	//if(isset($filters["Active"]) && $filters["Active"]!="")
	//{
	//	$sql .= CheckMultipleWheres($multipleWheres);
	//	$multipleWheres = true;
	//	if($filters["Active"] == 0) $sql .= " Active = 0 ";
	//	else if($filters["Active"]==1) $sql .= " Active = 1 ";
	//}
	if($nlimit > 0) $sql .= ' Limit ' . $nlimit;
	$sql .= ";";
	return $sql;
}
// ------ --------------------------------------------------------------
// Checks the boolean value $multipleWheres to see if it is set to true.
//  If true (i.e. there has already been a Where clause), it appends an
//  AND instead of a WHERE. 
// --------------------------------------------------------------------
function CheckMultipleWheres($multipleWheres){
	$sql = "";
	if($multipleWheres)
		{
			$sql .= " AND ";
		}
	else
		{
			$sql .= " WHERE ";
		} 
		return $sql;
}
?>
<?php
// --------------------------------------------------------------------
// workorderlib.hp:  library fucntions that deal with workorders.
//
// Created: 11/??/15 SH, BM ??
// --------------------------------------------------------------------

// --------------------------------------------------------------------
// Given the WorkOrderID, returns a associative array, with the following
// keys: (WorkOrderID, WorkOrderName, Description, DateRequested,
//    DateNeeded, Priority, DayEstimate, Revision, Requestor,
//    RequestingIPTLeadApproval, AssignedIPTLeadApproval, Project, 
//    RequestingIPTGroup, ReceivingIPTGroup, ProjectOfficeApproval,
//    ReviewedBy, AssignedTo, Completed, CompletedOn). False returned if
//    work order not found.
function GetWorkOrderInfo($workorderid)
{
    $loc = rmabs(__FILE__ . ".GetWorkOrderInfo");
    $sql = 'SELECT * FROM WorkOrders WHERE WorkOrderID=' . SqlClean($workorderid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}
// --------------------------------------------------------------------
// Given the WorkOrderID, returns a associative array, with the following
// keys: (PrereqID, PrevWorkOrderID, WorkOrderID). False returned if
//    work order prerequisite not found.
function GetWorkOrderPrereqInfo($workorderid)
{
    $loc = rmabs(__FILE__ . ".GetWorkOrderPrereqInfo");
    $sql = 'SELECT * FROM Prerequisites WHERE WorkOrderID=' . SqlClean($workorderid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}
// --------------------------------------------------------------------
// Given the WorkOrderID, returns a associative array, with the following
// keys: (TaskID, WorkOrderID, Quantity, Description, UnitPrice). False returned if
//    work order task not found.
function GetWorkOrderTasksInfo($workorderid)
{
    $loc = rmabs(__FILE__ . ".GetWorkOrderTasksInfo");
    $sql = 'SELECT * FROM WorkOrderTasks WHERE WorkOrderID=' . SqlClean($workorderid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}
// --------------------------------------------------------------------
// Given the WorkOrderID, returns a associative array, with the following
// keys: (FileID, WorkOrderID, FilePath). False returned if
//    work order task not found.
function GetWorkOrderFiles($workorderid)
{
    $loc = rmabs(__FILE__ . ".GetWorkOrderFiles");
    $sql = 'SELECT * FROM RelatedFiles WHERE WorkOrderID=' . SqlClean($workorderid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}
// --------------------------------------------------------------------
// Returns List of all IPTGroups in database.
function getIPTGroups($loc){
	$sql = "SELECT IPTGroupName FROM IPTGroup;";
	$result = SqlQuery($loc, $sql);
	$IPTGroupArray = array();
	while($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		$IPTGroupName = $row["IPTGroupName"];
		array_push($IPTGroupArray, $IPTGroupName);
	}
	return $IPTGroupArray;
}
// --------------------------------------------------------------------
// Returns list of all Projects in database;
function getAllProjects($loc){
	$sql = "SELECT ProjectName FROM Project;";
	$result = SqlQuery($loc, $sql);
	$ProjectArray = array();
	while($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		$ProjectName = $row["ProjectName"];
		array_push($ProjectArray, $ProjectName);
	}
	return $ProjectArray;
}
// --------------------------------------------------------------------
// Filters work orders based on the specified criteria
function FilterWorkOrders($view, $priority, $iptgroup, $status, $project)
{
	$loc = rmabs(__FILE__ . ".FilterWorkOrders");
	$multipleWheres = false;
	$sql = "SELECT ";
	if($view = "simple")
	{
		$sql .= " WorkOrderID, WorkOrderName, ReceivingIPTGroup ";
	}
	else
	{
		$sql .= " WorkOrderID, WorkOrderName, ReceivingIPTGroup, RequestingIPTGroup, Project, Priority, DateNeeded ";
	}
	$sql .=" FROM ActiveWorkOrders";
	if(isset($priority))
	{
		$sql .= "WHERE Priority = \'" . $priority ."\'" ;
		$multipleWheres = true;
	}
	$ProjectArray = array();
	
	if(isset($iptgroup))
	{
		if($multipleWheres)
		{
			$sql .= " AND ";
		}
		else
		{
			$sql .= "";
		}
		$sql .= "";
	}
	
	while($result->num_rows > 0)
	{
		$row = $result->fetch_assoc();
		$ProjectName = $row["ProjectName"];
		array_push($ProjectArray, $ProjectName);
	}
		$data = SqlQuery($loc, $sql);

	
	
	return $ProjectArray;
}

// --------------------------------------------------------------------
// Generates the Revision Letter(s) given the revision number.
function RevisionToStr($revision, $approved)
{
	$letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if($revision < 0) return "-?";
	if($revision == 0) return "**";
	$revision--;
	$dash = '*';
	if($approved) $dash = '-';
	if($revision < 26) return $dash . substr($letters, $revision, 1);
	$revision -= 26;
	$d2 = intval($revision / 26);
	$d1 = intval($revision % 26);
	return substr($letters, $d2, 1) . substr($letters, $d1, 1);
}

// --------------------------------------------------------------------
// Given a WID and it's revision number, generated the correctly 
// formatted string for a Work Order.
function WIDStr($wid, $revision, $approved=true)
{
	if($wid <= 0) return "W????" . RevisionToStr($revision, $approved);
	if($wid > 9999) return "W####" . RevisionToStr($revision, $approved);
	return sprintf("W%04d", $wid) . RevisionToStr($revision, $approved);
}

// --------------------------------------------------------------------
// Given a WID and it'w revision number, returns the HTML to 
// correctly fromat it (i.e., as a span.)
function WIDStrHtml($wid, $revision, $approved=true)
{
	return '<span class="wostr">' . WIDStr($wid, $revision, $approved) . '</span>' ;
}

// --------------------------------------------------------------------
// Create a new work order.  Iput is an assocative array of all fields
// in the work order table.  Returns a array of two values: WID of
// the new work order, or WID=0 and an error message.

function CreateNewWorkOrder($data)
{
	$loc = rmabs(__FILE__ . ".CreateNewWorkOrder");
    DenyGuest();  // Don't allow Guests to do this...

	if(empty($data["Title"])) return array(0, "Title cannot be empty.");
	if(empty($data["Description"])) return array(0, "Title cannot be empty.");
	if(empty($data["Priority"])) return array(0, "Priority cannot be empty.");
	if(empty($data["Project"])) return array(0, "Project cannot be empty.");
	if(empty($data["Requestor"])) return array(0, "Requestor cannot be empty.");
	if(empty($data["Receiver"])) return array(0, "Receiver cannot be empty.");
	if(empty($data["DateNeedBy"])) return array(0, "DateNeedBy cannot be empty.");
	if(empty($data["DateCreated"])) return array(0, "DateCreated cannot be empty.");

	if(!isset($data["Active"])) $data["Active"] = 1;

    // Check for duplicate title.
    $sql = 'SELECT WID FROM WorkOrders WHERE Title=?';
    $args = array($data["Title"]);
    $stmt = SqlPrepareAndExectue($loc, $sql, $args);
    $stmt->bind_result($arg_out_wid);
    $nr = 0;
    while($stmt->fetch()) {$nr++;}
    $stmt->close();
    if($nr > 0)
    {
        $msg = 'Unable to create Work Order. Duplicate Title. ';
        log_msg($loc, $msg);
        return array(0, $msg);
    }

    $sql = 'INSERT INTO WorkOrders ' .
           '(Title, Description, Priority, Project, Revision, ' .
   	       'Requestor, Receiver, AuthorID, DateCreated, DateNeedBy, ' .
   	       'Assigned, Approved, ApprovedByCap, Finished, Closed, Active) ';
	$sql .= ' VALUES(';
	$sql .= '  ?'; // . $data["Title"] . '"';
	$sql .= ', ?'; // . $data["Description"] . '"';
	$sql .= ', ?'; // . $data["Priority"] . '"';
	$sql .= ', ?'; // . $data["Project"] . '"';
	$sql .= ', '  . intval($data["Revision"]) ;
	$sql .= ', ?'; // . $data["Requestor"] . '"';
	$sql .= ', ?'; // . $data["Receiver"] . '"';
	$sql .= ', '  . intval($data["AuthorID"]) ;
	$sql .= ', "' . $data["DateCreated"] . '"';
	$sql .= ', "' . $data["DateNeedBy"] . '"';
	$sql .= ', '  . TFstr($data["Assigned"]);
	$sql .= ', '  . TFstr($data["Approved"]);
	$sql .= ', '  . TFstr($data["ApprovedByCap"]);
	$sql .= ', '  . TFstr($data["Finished"]);
	$sql .= ', '  . TFstr($data["Closed"]);
	$sql .= ', '  . TFstr($data["Active"]);
	$sql .= ')';

	$title = $data["Title"];
	$args = array($data["Title"], $data["Description"], $data["Priority"],
	              $data["Project"], $data["Requestor"], $data["Receiver"]);

    $stmt = SqlPrepareAndExectue($loc, $sql, $args);
    $stmt->close();
    log_msg($loc, 
       array("New WO added!  Title=" . $data["Title"] ,
       "AuthorID= " . $data["AuthorID"]));

    // Quicky find the new WO
    $sql = 'Select WID, Revision From WorkOrders WHERE Title = ?';
    $args = array($data["Title"]);
    $stmt = SqlPrepareAndExectue($loc, $sql, $args);
	$stmt->bind_result($arg_out_wid, $arg_out_revision);
    $nr = 0;
    $rows = array();
  	while($stmt->fetch()) 
	{
		$row["WID"] = $arg_out_wid;
		$row["Revision"] = $arg_out_revision;
		$rows[] = $row;
		$nr++;
	}
    if($nr != 1)
    {
    	//DieWithMsg($loc, array('Unable to find newly create workorder.', 'SQL=' . $sql));
    	dumpit($args);
    }
    $row = $rows[0];
    $wid = intval($row["WID"]);
    $revision = intval($row["Revision"]);

    return array($wid, true);
}

// --------------------------------------------------------------------
// Updates the primary part of the workorder.  Required Data fields 
// are: Title, Description, Project, Priority, DateNeedBy, Requestor,
// and Receiver.
function UpdateWorkOrder($wid, $data)
{
	$loc = rmabs(__FILE__ . ".UpdateWorkOrder");
	DenyGuest();  // Don't allow Guests to do this...
	$sql = 'UPDATE WorkOrders SET ';
	$sql .= '  Title = ?';
	$sql .= ', Description = ?';
	$sql .= ', Project = ?';
	$sql .= ', Priority = ?';
	$sql .= ', Requestor = ?';
	$sql .= ', Receiver = ?';
	$sql .= ', DateNeedBy = "' . $data["DateNeedBy"] .'"';
	$sql .= ' WHERE WID=' . intval($wid);

	$args = array($data["Title"], $data["Description"], $data["Project"],
	              $data["Priority"], $data["Requestor"], $data["Receiver"]);
	$stmt = SqlPrepareAndExectue($loc, $sql, $args);
	$stmt->close();
}

// --------------------------------------------------------------------
// Adds an Appended data record to a work order.  On error, 
// dies with message.

function AppendWorkOrderData($wid, $userid, $textinfo, $picid, $primary)
{
	$loc = rmabs(__FILE__ . ".AppendWorkOrderData");
	DenyGuest();  // Don't allow Guests to do this...
	if(intval($wid) <= 0) DieWithMsg($loc, 'Invalid WID. (' . $wid . ')');
	$date = date("Y-m-d");

	// Override primary if no pic to go with it.
	if($picid <= 0) $primary = false;

	// If this record is primary, remove primary flag on others.
	if($primary && $picid > 0)
	{
		$sql = 'UPDATE AppendedData SET PrimaryFile=0 WHERE WID=' . intval($wid);
		SqlQuery($loc, $sql);
	}


	$sql = 'SELECT WID FROM AppendedData WHERE WID=' . intval($wid);
	$result = SqlQuery($loc, $sql);
	$seq = $result->num_rows + 1;

	$sql = 'INSERT INTO AppendedData (WID, UserID, TextInfo, ';;
	$sql .= 'DateCreated, Sequence, PicID, PrimaryFile, Removed) ';
	$sql .= 'VALUES (' ;
	$sql .= '  ' . intval($wid);
	$sql .= ', ' . intval($userid);
	$sql .= ', ? ';
	$sql .= ', "' . $date . '"';
	$sql .= ', ' . intval($seq);
	$sql .= ', ' . intval($picid);
	$sql .= ', ' . TFstr($primary);
	$sql .= ', 0'; 
	$sql .= ')';

	$args = array($textinfo);
	$stmt = SqlPrepareAndExectue($loc, $sql, $args);
	$stmt->close();
}

// --------------------------------------------------------------------
// Appends a system note to a workorder without bumping the revision
// or attaching a picture.
function AttachSystemNote($wid, $text)
{
	DenyGuest();  // Don't allow Guests to do this...
	AppendWorkOrderData($wid, 0, $text, 0, false);
}

// --------------------------------------------------------------------
// Gets all the appended data for one WO.  For each record, finds 
// author name and data.
function GetAppendedData($wid)
{
	$loc = rmabs(__FILE__ . ".GetAppendedData");
	$sql = 'SELECT * FROM AppendedDataView WHERE WID=' . intval($wid);
	$result = SqlQuery($loc, $sql);
	$d = array();
	while($row = $result->fetch_assoc()) {
		$row["AuthorName"] = MakeAbbrivatedName($row);
    	$d[] = $row;
	}
	return $d;
}


// --------------------------------------------------------------------
// Gets evertying about one WO, and returns it in a assoc array.
// If the WO is not found, false is returned.  Note, non-active WOs
// are not found unless $override=true.

function GetWO($wid, $override=false)
{
	$loc = rmabs(__FILE__ . '.GetWO');
	$sql = "SELECT * From WorkOrders WHERE WID=" . intval($wid);
	if(!$override) $sql .= ' AND Active=1';
	$result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) return false;
    $data = $result->fetch_assoc();

    $data["IsApproved"] = $data["Approved"] || $data["ApprovedByCap"];
    $data["WIDStr"] = WIDStr($wid, $data["Revision"], $data["IsApproved"]);
    if(empty($data["AuthorID"])) 
    {
    	$data["AuthorInfo"] = array();
    	$data["AuthorName"] = "System";
    }
    else
    {
    	$data["AuthorInfo"] = GetUserInfo($data["AuthorID"]);
    	$data["AuthorName"] = "";
    	if(!empty($data["AuthorInfo"]))
    	{
    		$ai = $data["AuthorInfo"];
    		$data["AuthorName"] = MakeAbbrivatedName($ai); 
    	}
    }

    return $data;
}

// --------------------------------------------------------------------
// Gets the Primary PicID for a work order.  Returns an assoc array 
// with fields names from the AppendedDataView.
function GetPrimaryPicInfo($wid)
{
	$loc = rmabs(__FILE__ . ".GetPrimaryPicInfo");
	$sql = 'SELECT * FROM AppendedDataView WHERE WID=' . intval($wid);
	$sql .= ' AND PrimaryFile = 1';
	$result = SqlQuery($loc, $sql);
	if($result->num_rows <= 0) false;
	$row = $result->fetch_assoc();
	return $row;
}

// --------------------------------------------------------------------
// Adds one to the current revision level of a work order.  Logs msg
// on failure, but returns.
function IncrementRevision($wid, $username="")
{
	$loc = rmabs(__FILE__ . '.IncrementRevision');
	DenyGuest();  // Don't allow Guests to do this...
	$sql = "SELECT Revision FROM WorkOrders WHERE WID=" . intval($wid);
	$result = SqlQuery($loc, $sql);
	if($result->num_rows != 1) 
	{
		log_error($loc, 'Unable to find WO (' . $wid . ').');
		return false;
	}
	$row = $result->fetch_assoc();
	$revision = intval($row["Revision"]) + 1;
	$sql = 'UPDATE WorkOrders SET Revision=' . intval($revision);
	$sql .=' WHERE WID=' . intval($wid);
	SqlQuery($loc, $sql);
	if(!empty($username))
	{
		$msg ="Revision level increased by " . $username . '.';
		AttachSystemNote($wid, $msg);
	}
}


// --------------------------------------------------------------------
// Changes the status of the log and adds an new record to the 
// appended data, stating the change.
function ChangeWOStatus($wid, $username, $statusField, $state)
{
	$loc = rmabs(__FILE__ . "ChangeWOStatus");
	DenyGuest();  // Don't allow Guests to do this...
	$sql = 'UPDATE WorkOrders SET ' . $statusField . ' = ' . TFstr($state) . ' WHERE WID=' . intval($wid);
	SqlQuery($loc, $sql);
	$msg = 'Status "' . $statusField . '" changed to ' . TFstr($state) . ' by ' . $username . '.';
	AttachSystemNote($wid, $msg);
}

// --------------------------------------------------------------------
// Make an Assignement for a workorder.
function MakeAssignment($wid, $userid)
{
	$loc = rmabs(__FILE__ . "MakeAssigment");
	DenyGuest();  // Don't allow Guests to do this...
	RemoveAssignment($wid, $userid);
	$sql = 'INSERT INTO Assignments (WID, UserID) VALUES (' ;
	$sql .= intval($wid) . ', ' . intval($userid) . ')';
	SqlQuery($loc, $sql);
	UpdateAssignementCount($wid);
}

// --------------------------------------------------------------------
// Remove an Assignement for a workorder.
function RemoveAssignment($wid, $userid)
{
	$loc = rmabs(__FILE__ . "RemoveAssigment");
	DenyGuest();  // Don't allow Guests to do this...
	$sql = 'DELETE FROM Assignments WHERE WID=';
	$sql .= intval($wid) . ' AND UserID=' . intval($userid);
	SqlQuery($loc, $sql);
	UpdateAssignementCount($wid);
}

// --------------------------------------------------------------------
// Updates the "Asssigned" field in WorkOrders to be consistant
// with the number of workers assigned.
function UpdateAssignementCount($wid)
{
	$loc = rmabs(__FILE__ . "UpdateAssignementCount");
	DenyGuest();  // Don't allow Guests to do this...
	$workers = GetAssignedWorkers($wid);
	$assgined = 0;
	if(count($workers) > 0) $assgined = 1;
	$sql = "UPDATE WorkOrders SET Assigned=" . $assgined . ' WHERE WID=' . $wid;
	SqlQuery($loc, $sql);
}

// --------------------------------------------------------------------
// Get list of assigned workers to a WO.  Result is an array of arrays,
// where each element of the first array is an assoc array of field
// name of the user.
function GetAssignedWorkers($wid)
{
	$loc = rmabs(__FILE__ . "GetAssignedWorkers");
	$sql = 'SELECT * FROM AssignedUsersView WHERE WID=' . intval($wid);
	$result = SqlQuery($loc, $sql);
	$d = array();
	while($row = $result->fetch_assoc()) {
		$row["AbbrivatedName"] = MakeAbbrivatedName($row);
		$row["FullName"] = MakeFullName($row);
    	$d[] = $row;
	}
	return $d;
}

// --------------------------------------------------------------------
// Get list of all workers that can be assigned to a WO.  This is
// anybody with a "Worker" tag.
function GetAllWorkers()
{
	$loc = rmabs(__FILE__ . "GetAllWorkers");
	$sql = 'SELECT * FROM AllActiveUsersView ORDER BY LastName, FirstName';
	$result = SqlQuery($loc, $sql);
	$d = array();
	while($row = $result->fetch_assoc()) {
		$tags = ArrayFromSlashStr($row["Tags"]);
		if(CheckArrayForEasyMatch($tags, "worker"))
		{
			$row["AbbrivatedName"] = MakeAbbrivatedName($row);
    		$d[] = $row;
    	}
	}
	return $d;	
}

// --------------------------------------------------------------------
// Filter the list of workers by removing assigned workers.  Returns
// an new array of avaliable workers.  Note avaliable workers are
// assummed to have all fields, where was assigned workers can have
// a minimul set of fields.
function RemoveWorkers($AvaliableWorkers, $AssignedWorkers)
{
	$out = array();
	foreach($AvaliableWorkers as $w) 
	{
		$match = false;
		foreach($AssignedWorkers as $y)
		{
			if(isset($w["UserID"]) && isset($y["UserID"]))
			{
				if($w["UserID"] == $y["UserID"]) 
				{
					$match = true;
					break;
				}
				continue;
			}
			else if (isset($y["FirstName"]) && isset($y["LastName"]))
			{
				if($w["LastName"] == $y["LastName"])
				{
					if($w["FirstName"] == $y["FirstName"])
					{
						$match = true;
						break;
					}
					continue;
				}
				continue;
			}
			else if (isset($y["Name"]))
			{
				$wname = $w["FirstName"] . ' ' . $w["LastName"];
				if($wname == $y["Name"])
				{
					$match = true;
					break;
				}
				continue;
			}
			else if (isset($y["AbbrivatedName"]))
			{
				$wname = MakeAbbrivatedName($w);
				if($wname == $y["AbbrivatedName"]) 
				{
					$match = true;
					break;
				}
				continue;
			}

		}
		if($match) continue;
		$out[] = $w;
	}
	return $out;
}

// --------------------------------------------------------------------
// Given a list of avaliable workers, and an IPT team, resorts the
// list so that team members of the IPT team are at the top.
function SortForIPTTeam($workers, $ipt)
{
	if(empty($ipt)) return $workers;
	$newlist = array();
	foreach($workers as $w)
	{
		if($w["IPT"] == $ipt) $newlist[] = $w;
	}
	foreach ($workers as $w) 
	{
		if($w["IPT"] != $ipt) $newlist[] = $w;
	}
	return $newlist;
}

?>

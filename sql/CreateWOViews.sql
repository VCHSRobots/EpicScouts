/*
** -----------------------------------------------------------------------
** CreateWOViews.sql -- Defines views on the WorkOrder Tables.
**
** Created: 01/01/16 DLB
** -----------------------------------------------------------------------
*/

Use EpicTeam;

/* Drop in Order: */
drop view AllActiveUsersView;
drop view AssignedUsersView;
drop view AppendedDataView;
drop view AssignmentsView;
drop view ActiveWorkOrders;

Create View ActiveWorkOrders As
Select WID, Title, Description, Project, Priority, Revision, Requestor, Receiver, AuthorID, DateCreated,
       DateNeedBy, Assigned, Approved, ApprovedByCap, Finished, Closed, Active
       From WorkOrders WHERE Active=1;

Create View AssignmentsView As
Select ActiveWorkOrders.WID, ActiveWorkOrders.Title, ActiveWorkOrders.Description, ActiveWorkOrders.Project, 
       ActiveWorkOrders.Priority, ActiveWorkOrders.Revision,
       ActiveWorkOrders.Requestor, ActiveWorkOrders.Receiver, ActiveWorkOrders.AuthorID, ActiveWorkOrders.DateCreated, 
       ActiveWorkOrders.DateNeedBy, ActiveWorkOrders.Assigned, ActiveWorkOrders.Approved, ActiveWorkOrders.ApprovedByCap,
       ActiveWorkOrders.Finished, ActiveWorkOrders.Closed, ActiveWorkOrders.Active, Assignments.UserID
       FROM Assignments
       JOIN ActiveWorkOrders ON Assignments.WID = ActiveWorkOrders.WID;

Create View AppendedDataView As 
Select AppendedData.WID, AppendedData.UserID, AppendedData.TextInfo, AppendedData.DateCreated,
       AppendedData.Sequence, AppendedData.PicID, AppendedData.PrimaryFile, AppendedData.Removed,
       Users.LastName, Users.FirstName, Users.IPT, Users.Tags, Users.NickName
       FROM AppendedData
       LEFT JOIN Users On AppendedData.UserID = Users.UserID
       ORDER BY AppendedData.Sequence;

Create View AssignedUsersView As
Select Assignments.WID, Users.UserID, Users.UserName, Users.LastName, Users.FirstName, Users.NickName,
       Users.Tags, Users.IPT
       FROM Users 
       JOIN Assignments ON Assignments.UserID = Users.UserID;

Create View AllActiveUsersView As
Select Users.UserID, Users.UserName, Users.LastName, Users.FirstName, Users.NickName, Users.Tags, Users.IPT 
       FROM Users WHERE Users.Active = true;

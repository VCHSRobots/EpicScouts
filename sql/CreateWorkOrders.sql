/*
** -----------------------------------------------------------------------
** CreateWorkOrders.sql -- Defines the Workorder system 
**
** Created: 12/31/15  SS, DLB
** -----------------------------------------------------------------------
*/

Use EpicTeam;

/* Drop in Order:   ** See CreateWOViews for complete list. */
drop view AllActiveUsersView;
drop view AssignedUsersView;
drop view AppendedDataView;
drop view AssignmentsView;
drop table Assignments;
drop table AppendedData;
drop table WorkOrders;

create Table WorkOrders (
  WID int AUTO_INCREMENT PRIMARY KEY,
  Title varchar(80) NOT NULL UNIQUE,  /* Inforce uniqueness in code... */
  Description text,                   /* Describes all the work to do */
  Priority varchar(80),               /* Priority of the work */
  Project varchar(80),                /* Project for the work */
  Revision int,                       /* Revision number.  Starts at zero. */
  Requestor varchar(80),              /* IPT Team Name */
  Receiver varchar(80),               /* IPT Team Name of team going to do work */
  AuthorID int,                       /* UserID of person who created the WO */
  DateCreated date,                   /* Date WO created */
  DateNeedBy date,                    /* Date WO needed */
  Assigned boolean,                   /* True if WO is asspigned (See appended data for dates) */
  Approved boolean,                   /* True if WO is approved */
  ApprovedByCap boolean,              /* True if WO is approved by a captian */
  Finished boolean,                   /* True if WO is marked work done by ? */
  Closed boolean,                     /* True if WO is closed */
  Active boolean                      /* False if WO is deleted and is not to be used in any sort */
  );

/*Additional info created whenever a student must edit work order after it has been approved*/
create Table AppendedData(
    WID int,                     /* A foreign key to WorkOrders */
    UserID int,                  /* UserID of inputter of this data, or 0 if auto generated. */
    TextInfo text,               /* Main info that is being added. */
    DateCreated date,            /* Date the data was appended. */
    Sequence int,                /* To mantain order of entries. */
    PicID int,                   /* Foreign key to picture associated with data, zero if none.*/
    PrimaryFile boolean,         /* Marks uploaded file as most important */ 
    Removed boolean              /* Marks this record as removed. */
    );

create Table Assignments(
    WID int,                     /* Foreign key to WorkOrders */
    UserID int                   /* UserID of person assigned to do the work on WO. */
	);    

/*
drop table Prerequisites; 
create Table Prerequisites (
   PrereqID int AUTO_INCREMENT PRIMARY KEY,
   PrevWorkOrderID int,
   WorkOrderID int
); 

drop table RelatedFiles;
create Table RelatedFiles (
   FileID int AUTO_INCREMENT PRIMARY KEY,
   WorkOrderID int,
   FilePath varchar(200)
   );
*/
    
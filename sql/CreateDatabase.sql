/* drop Database EpicTeam; */
Create Database EpicTeam;
Use EpicTeam;

/* Clean up in order:  */
drop view UserView;
drop table UserPics;
drop table Pictures;
drop table Prefs;
drop table Users;

create table Users
(
   UserID int AUTO_INCREMENT PRIMARY KEY,
   UserName varchar(40) NOT NULL UNIQUE,     /* Cannot be changed! */
   PasswordHash varchar(40),                
   LastName varchar(80),
   FirstName varchar(80),
   NickName varchar(80),
   Title varchar(80),
   Email varchar(200),
   Tags varchar(120),                        
   IPT varchar(80),                          /* Perferred IPT */
   Active boolean
);

insert into Users (UserName, PasswordHash, LastName, FirstName, NickName, Title, Email, Tags, IPT, Active)
    values("dbrandon", "41w0Haer3yB3", "Brandon", "Dalbert", "Dal", "Mentor", "dalbrandon@gmail.com", "Member/Editor/Admin", "", TRUE);
    
create Table Prefs
(
   UserID int,
   PrefName varchar(32),     /* Name of the preference */
   PrefValue varchar(256),    /* Value of the preference.  All preferences are stored as strings. */
   CONSTRAINT fk_Users FOREIGN KEY (UserID) REFERENCES Users(UserID)
);    
    
create Table Pictures
(
   PicID int AUTO_INCREMENT PRIMARY KEY,  /* From this, path and URLs can be calculated. */
   DateOfUpload datetime,    /* Date the the file was uploaded */
   FileStatus int,           /* 0=no file yet, pending upload, 1=file okay, 2=deleted/error */
   FileSize int,             /* Original Filesize */
   Width int,                /* Original Width of the photo */
   Height int                /* Original Height of the photo */
);

create Table UserPics
(
   PicID int,                /* Foregin key, PicID of existing picture */
   UserID int                /* Foregin key, ID of the user  who is the subject of the pic. */
);

Create View UserView As
Select Users.UserID, Users.UserName, Users.PasswordHash, Users.LastName, Users.FirstName, Users.NickName, 
       Users.Title, Users.Email, Users.Tags, Users.Active, Users.IPT, UserPics.PicID
       FROM Users
       LEFT JOIN UserPics ON UserPics.UserID = Users.UserID;
       

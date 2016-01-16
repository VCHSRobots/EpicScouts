<?php
// --------------------------------------------------------------------
// userlib:  library fucntions that deal with users and permissions.
//
// Created: 11/22/14 DLB
// Updated: 11/29/14 DLB -- Updated for Epic Admin Website
// Update:  01/01/16 DLB -- Updated for Epic Team Website
// --------------------------------------------------------------------

require_once "config.php";
require_once "databaselib.php";
require_once "loglib.php";

// The fuctions in this file are mostly concerned with database
// interaction for the currently logged in user.  Note, that, upon
// a successful login, information about the user is stored in the
// $_SESSION super variable.   This includes preference info. If
// the user changes preference info, it is updated in the $_SESSION
// variable as well as written to the database.

// --------------------------------------------------------------------
// Starts a proper login.  If credentials are not found, false returned. 
// Otherwize true returned, and login session is started.  Set $bypass
// true to ingore password checking.
function StartLogin($name, $pw, $bypass)
{
    global $config;
    $loc = "userlib.php->StartLogin";
    $_SESSION["LoggedIn"] = false;
    
    log_msg($loc, "checking=" . $name . ', bypass=' . TFstr($bypass));
    
    $sql = 'SELECT UserID, UserName, PasswordHash, LastName, FirstName, Tags, Active FROM Users ';
    $sql .= 'WHERE UserName="' . SqlClean($name) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows < 1) 
    { 
        log_msg($loc, 'Login failure for username: "' . $name. '". User not found.');
        return false; 
    }
    $row = $result->fetch_assoc();
    if(empty($row["Active"])) 
    {
        log_msg($loc, 'Login failure for username "' . $name . '". User not active.');
        return false;
    }

    $pwHash = crypt($pw, $config["Salt"]);
    if($row["PasswordHash"] != $pwHash) 
    {
        if(!$bypass)
        {
            log_msg($loc, 'Login failure for username "' . $name . '". Password mismatch. ');
            return false;
        }
        log_msg($loc, 'User "' . $name . '" used bypass feature to avoid password match.');
    }

    $_SESSION["LoggedIn"] = true;
    $_SESSION["Login_Time"] = time();
    $_SESSION["Login_UserID"] = $row["UserID"];
    $_SESSION["Login_UserName"] = $name;
    $_SESSION["Login_LastName"] = $row["LastName"];
    $_SESSION["Login_FirstName"] = $row["FirstName"];
    $_SESSION["Login_Tags"] = ArrayFromSlashStr($row["Tags"]);
    $_SESSION["Login_IsAdmin"]  = CheckForTag("admin");
    $_SESSION["Login_IsGuest"] = CheckForTag("guest");
    $_SESSION["Login_IsEditor"] = CheckForTag("editor");
    $_SESSION["Login_IsIPTLead"] = CheckForTag("iptlead");
	$_SESSION["Login_IsCaptain"] = CheckForTag("captain");
	$_SESSION["Login_IsMentor"] = CheckForTag("mentor");
    $_SESSION["Login_IsWorker"] = CheckForTag("worker");

    // Get all the current preferences.
    $_SESSION["Prefs"] = GetPrefsForUser(GetUserID());
    
    $lines = array();
    array_push($lines, ">>>>>>>>>>> " . $row["LastName"]. ', ' . $row["FirstName"] );
    array_push($lines, "New Login!  UserName=" . $row["UserName"] . ',   UserID=' . $row["UserID"]);
    array_push($lines, "IP Address= " . $_SERVER["REMOTE_ADDR"]  . "    Tags=" . $row["Tags"]);
    array_push($lines, "Browser=" . $_SERVER["HTTP_USER_AGENT"]);
    log_msg($loc, $lines);
    return true;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in. 
function IsLoggedIn()
{
    if(empty($_SESSION["LoggedIn"])) { return false; }
    if($_SESSION["LoggedIn"]) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as an Admin.
function IsAdmin()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsAdmin"])) { return false; }
    if($_SESSION["Login_IsAdmin"] === true) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as a Guest.
function IsGuest()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsGuest"])) { return false; }
    if($_SESSION["Login_IsGuest"] === true) { return true; }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is logged in as an Editor.
function IsEditor()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsEditor"])) { return false; }
    if($_SESSION["Login_IsEditor"] === true) { return true; }
    return false;
}
// --------------------------------------------------------------------
// Returns true if the client is logged in as an IPT Lead.
function IsIPTLead()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsIPTLead"])) { return false; }
    if($_SESSION["Login_IsIPTLead"] === true) { return true; }
    return false;
}
// --------------------------------------------------------------------
// Returns true if the client is logged in as an Editor.
function IsMentor()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsMentor"])) { return false; }
    if($_SESSION["Login_IsMentor"] === true) { return true; }
    return false;
}
// --------------------------------------------------------------------
// Returns true if the client is logged in as an Editor.
function IsCaptain()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsCaptain"])) { return false; }
    if($_SESSION["Login_IsCaptain"] === true) { return true; }
    return false;
}
// --------------------------------------------------------------------
// Returns true if the client is logged in as a Worker.
function IsWorker()
{
    if(!IsLoggedIn()) { return false; }
    if(!isset($_SESSION["Login_IsWorker"])) { return false; }
    if($_SESSION["Login_IsWorker"] === true) { return true; }
    return false;
}
//// --------------------------------------------------------------------
//// Returns iptgroup if current user is an IPTLead .
//function getIPTGroup($loc){
//    if(IsIPTLead())
//    {
//        $userID = GetUserID();
//        $sql = "SELECT IPTGroupName FROM IPTGroup WHERE IPTLeadID = \"" . $userID . "\";";
//        $result = SqlQuery($loc, $sql);
//        if ($result->num_rows > 0)
//        {
//            $row = $result->fetch_assoc();
//            $IPTGroupName = $row["IPTGroupName"];
//            return $IPTGroupName;
//        }
//        else{
//            return false;
//        }
//    }
//    else{
//        return false;
//    }
//}

// --------------------------------------------------------------------
// Returns true if one of the tags is found in the user's tag list.
function CheckForTag($tag)
{
    if(!isset($_SESSION["Login_Tags"])) { return false; }
    foreach($_SESSION["Login_Tags"] as $t)
    {
        if(strtolower($t) == strtolower($tag)) { return true; }
    }
    return false;
}

// --------------------------------------------------------------------
// Given the slashed list of tags, checks to see if given tag is in
// it.
function CheckRawTagList($tag, $SlashedList)
{
    $taglist = ArrayFromSlashStr($SlashedList);
    foreach($taglist as $t)
    {
        if(strtolower(trim($t)) == strtolower(trim($tag))) return true;
    }
    return false;
}

// --------------------------------------------------------------------
// Returns true if the client is currently logged in.  Will automatically logout after
// 2 days.  If not Logged in, the page will show a login link, but the fuction will not
// return to the caller.
function CheckLogin()
{
    if(!IsLoggedIn())
    {
        log_msg("userlib.php->CheckLogin", array("User is not logged in!  Privilege  violation!",
        "IP Address=" . $_SERVER["REMOTE_ADDR"]));
        include "forms/keepout_form.php";
        exit;
    }
    return true;
}

// --------------------------------------------------------------------
// Returns true if the client has admin privileges.  Otherwise, it will
// not return and instead will given an error message to the user with a
// link back to the home page.
function CheckAdmin()
{
    if(IsAdmin()) return true;
    log_msg("userlib.php->CheckAdmin", "User is not Admin!  Privilege  violation!");
    include "forms/noprivilege_form.php";
    exit;
}

// --------------------------------------------------------------------
// Returns true if the client has editor privileges.  Otherwise, it will
// not return and instead will give an error message to the user with a
// link back to the home page.
function CheckEditor()
{
    if(IsEditor()) return true;
    log_msg("userlib.php->CheckEditor", "User is not Editor!  Privilege  violation!");
    include "forms/noprivilege_form.php";
    exit;
}

// --------------------------------------------------------------------
// Returns true if the client has captain privileges.  Otherwise, it will
// not return and instead will give an error message to the user with a
// link back to the home page.
function CheckCaptain()
{
    if(IsCaptain()) return true;
    log_msg("userlib.php->CheckEditor", "User is not Captain!  Privilege  violation!");
    include "forms/noprivilege_form.php";
    exit;
}

// --------------------------------------------------------------------
// Checks to see if the user is a guest, and if so, sends them to the
// Denied access page.
function DenyGuest($reason="")
{
    $loc = rmabs(__FILE__ . "DenyGuest");
    if(!IsGuest()) return;
    if(empty($reason)) 
    {
        $reason = "Since you are flagged as a guest, you do not have privilege to change anything.";
    }
    DieNice($loc, $reason);
}

// --------------------------------------------------------------------
// Set Masquerader name.  Special feature that must manage session
// in a careful way.
function SetMasquerader($username)
{
    $_SESSION["Masquerade_User"] = $username;
}

// --------------------------------------------------------------------
// Returns true if masquerading.  False otherwise.
function IsMasquerading()
{
    if(isset($_SESSION["Masquerade_User"])) return true;
    return false;
}

// --------------------------------------------------------------------
// Gets the name of a masquerader, if any. Empty otherwise.
function GetMasquerader()
{
    if(isset($_SESSION["Masquerade_User"])) 
    {
        return $_SESSION["Masquerade_User"];
    }
    return "";
}

// --------------------------------------------------------------------
// Returns the name of the user that is currently logged in, in a nice format.
function UserFormattedName()
{
    if(IsLoggedIn()){ return $_SESSION["Login_FirstName"] . " " . $_SESSION["Login_LastName"]; }
    return "";
}

// --------------------------------------------------------------------
// Returns the name of the user that is currently logged in, in 
// "LastName, FirstName" format.  If no user logged in, empty
// string returned.
function UserLastFirstName()
{
    if(IsLoggedIn()){ return $_SESSION["Login_LastName"] . ", " . $_SESSION["Login_FirstName"]; }
    return "";
}

// --------------------------------------------------------------------
// Returns the user ID, or zero if not logged in.
function GetUserID()
{
    if(!IsLoggedIn()) return 0;
    return intval($_SESSION["Login_UserID"]);
}

// --------------------------------------------------------------------
// Returns the user name of the currently logged in user, or zero if
// no logged in.
function GetUserName()
{
    if(!IsLoggedIn()) return 0;
    return $_SESSION["Login_UserName"];
}

// --------------------------------------------------------------------
// Gets a user ID from a username.  If username not found, false
// returned.
function GetUserIDFromName($username)
{
    $loc = "userlib.php->GetUserIDFromName";
    $sql = 'SELECT UserID From Users WHERE UserName = "' . SqlClean($username) . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows <= 0) return false;
    $row = $result->fetch_assoc();
    $id = $row["UserID"];
    return $id;
}

// --------------------------------------------------------------------
// Given a user's ID, returns the user's assocated IPT.  If cannot be
// found, and empty string is returned.
function GetUserIPT($userid)
{
    if($userid <= 0) return "";
    $sql = 'SELECT IPT From Users WHERE UserID = ' . intval($userid);
    $result = SqlQuery(rmabs(__FILE__), $sql);
    if($result->num_rows <= 0) return "";
    $row = $result->fetch_assoc();
    return $row["IPT"];
}

// --------------------------------------------------------------------
// Changes the password of the current user.
function ChangePassword($pw)
{
    global $config;
    $loc = "userlib.php-ChangePassword";
    if(!IsLoggedIn()) { return false; }
    if(empty($pw)) { return false; }
    
    $pwhash = crypt($pw, $config["Salt"]);
    $sql = "UPDATE Users SET PasswordHash=\"" . $pwhash . "\" WHERE UserID=" . GetUserID();
    $result = SqlQuery($loc, $sql);
    log_msg($loc, "Password Changed.");
    return true;
}

// --------------------------------------------------------------------
// Gets a single preference for the current user.  If the preference
// does not exist, the given default is returned.
function GetPref($PrefName, $default="")
{
    if(!IsLoggedIn()) { return $default; }
    if(!isset($_SESSION["Prefs"])) {
        DieWithMsg("userlib.php->GetPref", '$_SESSION ["Prefs"] Not set!'); 
    }
    if(!isset($_SESSION["Prefs"][$PrefName])) { return $default; }
    return $_SESSION["Prefs"][$PrefName];
}

// --------------------------------------------------------------------
// Saves one preference for the current user.  The preference is saved
// to the database as well as to the current session.
function SavePref($PrefName, $PrefValue)
{
    if(!IsLoggedIn()) 
    {
        DieWithMsg("userlib.php->SavePref", "Call to SavePref while not logged in.");
    }
    if(!isset($_SESSION["Prefs"])) 
    {
        DieWithMsg("userlib.php->SavePref", '$_SESSION["Prefs"] Not set!'); 
    }
    $_SESSION["Prefs"][$PrefName] = $PrefValue;
    SavePrefsForUser(GetUserID(), $_SESSION["Prefs"]);
}

// --------------------------------------------------------------------
// Creates a new user.  Returns true if successful.  Otherwise
// returns an error message that is suitable for display.
// The input is an associtive array with the following
// required keys: LastName, FirstName, UserName, Password.  Optional
// keys are NickName, Title, BadgeID, Email, Tags, IPT, Active.  If failure
// results from bad inputs, or database problems -- DieWithMsg is called.
// Non-serious failurs return an explianation string.  On success, true
// is returned.
function CreateNewUser($params)
{
    global $config;
    $loc = "userlib.php->CreateNewUser";
    DenyGuest();  // Don't allow Guests to do this...

    if(empty($params["LastName"])) {return "Last name cannot be empty."; }
    if(empty($params["FirstName"])) {return "First name cannot be empty."; }
    if(empty($params["UserName"])) {return "Username cannot be empty."; }
    if(empty($params["PasswordHash"]))
    {
        if(empty($params["Password"])) {return "Password cannot be empty."; }
    }
    
    $username  = SqlClean($params["UserName"]);
    $lastname  = SqlClean($params["LastName"]);
    $firstname = SqlClean($params["FirstName"]);

    $nickname = "";
    $title = "";
    $email = "";
    $tags = "";
    $ipt = "";
    $active = false;
    if(isset($params["NickName"])) { $nickname = SQLClean($params["NickName"]); }
    if(isset($params["Title"]))    { $title    = SQLClean($params["Title"]); }
    if(isset($params["Email"]))    { $email    = SQLClean($params["Email"]); }
    if(isset($params["Tags"]))     { $tags     = SQLClean($params["Tags"]); }
    if(isset($params["IPT"]))      { $ipt      = SqlClean($params["IPT"]); }
    if(isset($params["Active"]))   { $active   = $params["Active"]; }
    
    // Check for duplicate username.
    $sql = 'SELECT UserID FROM Users WHERE UserName="' . $username . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows > 0)
    {
        $msg = 'Unable to add new user. Duplicate username. (' . $username . ')';
        log_msg($loc, $msg);
        return $msg;
    }
    
    // Check for duplicate first/last name
    $sql = 'SELECT UserID FROM Users WHERE LastName="' . 
           $lastname  . '" AND FirstName="' .
           $firstname . '"';
    $result = SqlQuery($loc, $sql);
    if($result->num_rows > 0)
    {
        $msg = 'Unable to add new user. Duplicate first/last name. (' .
               $lastname . ', ' . $firstname . ')';
        log_msg($loc, $msg);
        return $msg;
    }
    
    // Build the sql to add user.
    $pwhash = "";
    if(!empty($params["PasswordHash"])) $pwhash = $params["PasswordHash"];
    else $pwhash = crypt($params["Password"], $config["Salt"]);
    $sql = 'INSERT INTO Users (UserName, PasswordHash, LastName, FirstName, NickName, ' .
           'Title, Email, Tags, IPT, Active) ';
    $sql .= ' VALUES(';
    $sql .= '  "' . $username  . '"';
    $sql .= ', "' . $pwhash    . '"';
    $sql .= ', "' . $lastname  . '"';
    $sql .= ', "' . $firstname . '"';
    $sql .= ', "' . $nickname  . '"';
    $sql .= ', "' . $title     . '"';
    $sql .= ', "' . $email     . '"';
    $sql .= ', "' . $tags      . '"';
    $sql .= ', "' . $ipt       . '"';
    $sql .= ', '  . TFstr($active);
    $sql .= ')';

    $result = SqlQuery($loc, $sql);
    log_msg($loc, 
       array("New User added!  Username=" . $username ,
       "Full name= " . $lastname . ', ' . $firstname, 
       "tags=" . $tags . ", Active=" . TFstr($active)));
    return true;
}

// --------------------------------------------------------------------
// Given the user id, returns a associative array, with the following
// keys: UserID, UserName, LastName, FirstName, NickName, Email, 
// Tags, IPT, Active. False returned if user not found.
function GetUserInfo($userid)
{
    if(intval($userid <= 0)) return false;
    $loc = rmabs(__FILE__ . ".GetUserInfo");
    $sql = 'SELECT * FROM UserView WHERE UserID=' . SqlClean($userid);
    $result = SqlQuery($loc, $sql);
    if($result->num_rows != 1) { return false; }
    $row = $result->fetch_assoc();
    return $row;
}

// --------------------------------------------------------------------
// Tries to retrive a UserID given info about the user.  Input can be
// a field name of "UserID", "FullName", "LastName", "UserName",
// "AbbrivatedName".  A UserID is returned on success, or false if
// cannot find the user.
function FindUser($fieldname, $info)
{
    $loc = rmabs(__FILE__ . ".FindUser");
    if($fieldname == "UserID")
    {
        $userinfo = GetUserInfo(intval($info));
        if(!$userinfo) return false;
        return $userinfo["UserID"];
    }
    if($fieldname == "FullName")
    {
        // Very inefficent but can work.
        // Must do it this way cause some people have three parts to their name.
        $sql = 'SELECT * FROM Users';
        $result = SqlQuery($loc, $sql);
        while($row = $result->fetch_assoc())
        {
            $fullname = $row["FirstName"] . ' ' . $row["LastName"];
            if(trim($info) == trim($fullname)) return $row["UserID"];
        }
        return false;
    }
    if($fieldname == "UserName")
    {
        $sql = 'SELECT * FROM Users WHERE UserName="' . SqlClean($info) . '"'; 
        $result = SqlQuery($loc, $sql);
        if($result->num_rows != 1) { return false; }
        $row = $result->fetch_assoc(); 
        return $row["UserID"];
    }
    if($fieldname == "LastNameFirst")
    {
        $words = explode(",", $info);
        if(count($words) != 2) return false;
        $lastname = trim($words[0]);
        $firstname = trim($words[1]);
        $sql = 'SELECT * FROM Users WHERE LastName="' . $lastname . '" AND FirstName="' . $firstname . '"'; 
        $result = SqlQuery($loc, $sql);
        if($result->num_rows != 1) { return false; }
        $row = $result->fetch_assoc();
        return $row["UserID"];        
    }
    log_error($loc, "Should be unreachable code. ");
    return false;
}

// --------------------------------------------------------------------
// Given an array of user info (i.e, an array of field names with user
// info), appempts to construct the abbrivated name.
function MakeAbbrivatedName($row)
{
    $first = "";
    $last  = "";

    if(!empty($row["FirstName"])) $first = $row["FirstName"];
    if(!empty($row["LastName"])) $last = $row["LastName"];

    $firstletter = "";
    if(strlen($first) >=1) $firstletter = strtoupper(substr($first, 0, 1) . '. ');
    $name = $firstletter . $last;
    if(empty($name)) $name = " ";
    return $name;
}

// --------------------------------------------------------------------
// Given an array of user info (i.e, an array of field names with user
// info), appempts to construct the abbrivated name.
function MakeFullName($row)
{
    $first = "";
    $last  = "";

    if(!empty($row["FirstName"])) $first = $row["FirstName"];
    if(!empty($row["LastName"])) $last = $row["LastName"];

    return $first . ' ' . $last;
}

// --------------------------------------------------------------------
// Updates info about a user.  The input is a param_list for the 
// fields that need updating. Possible fields names are:
// UserName, Password, LastName, FirstName, NickName, Title, BadgeID, 
// Email, Tags, IPT, Active.  Note that the UserName of an account cannot
// be changed. If it is included in the param_list, it is used to find
// the account to change.  If, however, $userid is proivded, then that
// is used the find the account. True is returned on success, otherwise
// an error message suitable for display is returned.  Bad SQL errors
// will die. 

function UpdateUser($param_list, $userid=0)
{
    global $config;
    $loc = "userlib.php->UpdateUser";
    $pwchanged = false;
    
    $fields = array(array("LastName",     "str"),
                    array("FirstName",    "str"),
                    array("PasswordHash", "str"),
                    array("NickName",     "str"),
                    array("Title",        "str"),
                    array("Email",        "str"),
                    array("Tags",         "str"),
                    array("IPT",          "str"),
                    array("Active",       "bool"));
    
    if($userid != 0)
    {
        $sql = "SELECT * FROM Users WHERE UserID=" . intval($userid);
        $result = SqlQuery($loc, $sql);
        if($result->num_rows <= 0) 
        {
            $error_msg = "Unable to update user. UserID=" . intval($userid) . " not found.";
            log_msg($loc, $error_msg);
            return $error_msg;
        }
    }
    else
    {
        if(!IsFieldInParamList("UserName", $param_list))
        {
            $error_msg = 'Unable to update user. No UserName or UserID Given.';
            log_msg($loc, $error_msg);
            return $error_msg;
        }
        $username = GetValueFromParamList($param_list, "UserName");
        $sql = 'SELECT * FROM Users WHERE UserName="' . SqlClean($username) . '"';
        $result = SqlQuery($loc, $sql);
        if($result->num_rows <= 0) 
        {
            $error_msg = 'Unable to update user. UserName="' . SqlClean($username) . '" not found.';
            log_msg($loc, $error_msg);
            return $error_msg;
        }
        $row = $result->fetch_assoc();
        $userid = intval($row["UserID"]);
    }
            
    // At this point, move all values into a seperate array, but treat password special.
    $data = array();
    $c = 0;
    foreach($param_list as $param_spec)
    {
        if(!isset($param_spec["FieldName"])) continue;
        if(!isset($param_spec["Value"])) continue;
        if($param_spec["FieldName"] == "Password") 
        {
            $pw = $param_spec["Value"];
            if(empty($pw)) continue;
            $v = crypt($pw, $config["Salt"]);
            $pwchanged = true;
            $fn = "PasswordHash";
            $data[$fn] = $v;
            $c++;
            continue;  
        }
        $fn = $param_spec["FieldName"];
        $v  = $param_spec["Value"];
        $data[$fn] = $v;
        $c++;
    }
    
    if($c <= 0) 
    {
        $error_msg = "Unable to update user. UserID=" . intval($userid) . ". Nothing to update.";
        log_msg($loc, $error_msg);
        return $error_msg;
    }
    
    // At this point, we have a userid that we can count on, and the data.
    $sql  = 'UPDATE Users SET ';
    $sql  .= GenerateSqlSet($data, $fields);
    $sql  .= " WHERE UserID=" . intval($userid);
    SqlQuery($loc, $sql);
    
    $msg = 'Info for User ' . $userid . ' updated by ' . GetUserName() . '. ';
    if($pwchanged) $msg .= '(Including a password change.)';
    log_msg($loc, $msg);
    return true;
}


?>
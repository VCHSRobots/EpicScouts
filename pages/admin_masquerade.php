<?php
// --------------------------------------------------------------------
// admin_masquerade.php -- page to allow masquerading.
//
// Created: 12/05/14 DLB
// Updated: 12/29/14 DLB -- Hacked from Epic Scouts...
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();

$error_msg = "";

$sql = 'SELECT * FROM Users ORDER BY LastName, FirstName';
$result = SqlQuery($loc, $sql);
$names = array();
$names[] = "";
while($row = $result->fetch_assoc())
{
    $names[] = $row["LastName"] . ', ' . $row["FirstName"];
}

$param_list = array(
array("FieldName" => "User",  "FieldType" => "Selection", "Selection" => $names, "Caption" => "User Accout"));
$doform = true;


if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    DenyGuest();
    if(empty($_POST["User"])) goto GenerateHtml;
    $lastnamefirst = $_POST["User"];
    $newuserid =  FindUser("LastNameFirst", $lastnamefirst);
    if(!$newuserid)
    {
        $error_msg = "Unable to find user id. (Two users with same name?)";
        $doform = true;
        goto GenerateHtml; 
    }
    $newuserinfo = GetUserInfo($newuserid);
    $newusername = $newuserinfo["UserName"];
    
    $currentuser = GetUserName();
    log_msg($loc, 'User ' . $currentuser . ' is attemping to masquerade as ' . $newusername);
    session_unset();
    session_destroy();
    session_start();
    $okay = StartLogin($newusername, "", true);
    if($okay === false)
    {
        log_msg($loc, "Login failure for masquerade.  Starting ALL over.");
        session_unset();
        session_destroy();
        JumpToPage("pages/login.php");
    }
    SetMasquerader($currentuser);
    JumpToPage("pages/welcome.php");
}
   
GenerateHtml:   
include "forms/header.php";
include "forms/nav_form.php";
include "forms/admin_menubar.php";
include "forms/admin_masquerade_form.php";
include "forms/footer.php";

?>
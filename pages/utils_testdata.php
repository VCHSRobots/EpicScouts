<?php
// --------------------------------------------------------------------
// utils_testdata.php -- page to generate test data 
//
// Created: 01/01/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$error_msg = "";

if( $_SERVER["REQUEST_METHOD"] == "POST") 
{
    if(isset($_POST["WO"])) 
    {
        DenyGuest();
        $success_msg = make_rand_wo();
        goto GenerateHTML;
    }
    if(isset($_POST["Accounts"])) 
    {
        DenyGuest();
        $success_msg = make_rand_accounts();
        goto GenerateHTML;
    }
    if(isset($_POST["Append"])) 
    {
        DenyGuest();
        $error_msg = "Not implemented yet.";
        goto GenerateHTML;
    }
}

GenerateHTML:

include "forms/header.php";
include "forms/nav_form.php";
include "forms/utils_menubar.php";
echo '<div class="content_area">';
include "forms/utils_testdata_form.php";
echo '</div>';
include "forms/footer.php";

// --------------------------------------------------------------------
// Returns array of all UserIDs.
function get_all_accounts()
{
    $loc = rmabs(__FILE__ . ".get_all_accounts");
    $sql = "SELECT UserID from Users";
    $result = SqlQuery($loc, $sql);
    while($row = $result->fetch_assoc())
    {
        $aws[] = intval($row["UserID"]);
    }
    return $aws;
}

// --------------------------------------------------------------------
// Make random work order.
function make_rand_wo()
{
    global $WOProjects;
    global $WOIPTeams;
    global $WOPriorities;
    $lines = file("docs/randomtext.txt", FILE_USE_INCLUDE_PATH);
    $titles = file("docs/randomnames.txt", FILE_USE_INCLUDE_PATH);
    $accounts = get_all_accounts();
    $revs = array(0,0,0,1,1, 1, 1, 1, 1, 2, 2, 2, 3, 3, 3, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12);
    $pris = array(0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 2, 2, 3, 3);
    $app = array(false, false, false, true, true, true, true, true, true, true);

    $nsuccess = 0;
    $na = 0;
    $nas = 0;
    $nf = 0;
    $nc = 0;
    $nact = 0;
    $nacap = 0;
    $nwo = count($titles);
    foreach($titles as $t)
    {
        $data = array();

        $data["Title"] = trim($t);
        $data["Description"] = trim($lines[rand(0, count($lines) - 1)]);
        $data["Priority"] = $WOPriorities[$pris[rand(0, count($pris) - 1)]];
        $data["Project"] = $WOProjects[rand(1, count($WOProjects) - 1)];
        $data["Revision"] = $revs[rand(0, count($revs) - 1)];
        $data["Requestor"] = $WOIPTeams[rand(1, count($WOIPTeams) - 1)];
        $data["Receiver"] = $WOIPTeams[rand(1, count($WOIPTeams) - 1)];
        $data["AuthorID"] = $accounts[rand(0, count($accounts) - 1)];
        $data["DateCreated"] = date('Y-m-d', time() - (rand(0, 30) * 24 * 3600));
        $data["DateNeedBy"] = date('Y-m-d', time() + (rand(0, 30) * 24 * 3600));
        $data["Approved"] = false;
        $data["ApprovedByCap"] = false;    
        $data["Assigned"] = false;  
        $data["Finished"] = false;
        $data["Closed"] = false;
        $data["Active"] = true;  

        if($data["Revision"] > 0) {
            $data["Approved"] = (rand(0, 100) > 20);
            $data["ApprovedByCap"] = (rand(0, 100) > 85);
        }
        if($data["Approved"] || $data["ApprovedByCap"]) 
        {
            $data["Assigned"] = (rand(0, 100) > 30);
            $data["Finished"] = (rand(0, 100) > 60);
        }
        if($data["Finished"])
        {
            $data["Closed"] = (rand(0, 100) > 20);
        }
        if(!$data["Closed"]) $data["Closed"] = (rand(0, 100) > 95);
        $data["Active"] = !(rand(0, 100) > 98);

        $result = CreateNewWorkOrder($data);
        if($result[1] === true)
        {
            $nsuccess++;
            if($data["Approved"]) $na++;
            if($data["ApprovedByCap"]) $nacap++;
            if($data["Assigned"]) $nas++;
            if($data["Finished"]) $nf++;
            if($data["Closed"]) $nc++;
            if(!$data["Active"]) $nact++;
        }
        else echo '<br>' . $result[1] . $data["Title"];

    }
    $msg = "WOs added=" . $nsuccess . "\n";
    $msg .= "App, CapApp= " . $na . ', ' . $nacap . ' ' . "\n";
    $msg .= "Asgnd= " . $nas . ',  Fin=' . $nf . ' Csd=' . $nc . ", NotAct=" . $nact;
    return $msg;


}

// --------------------------------------------------------------------
// Make random accounts
function make_rand_accounts()
{
    global $WOIPTeams;
    $names = random_names();

    $n_names = count($names) / 2;
    $d = array();
    $nsuccess = 0;
    for($i = 0; $i < $n_names; $i++) 
    {
        $d["FirstName"] = $names[$i * 2];
        $d["LastName"] = $names[($i*2) + 1];
        $d["UserName"] = strtolower(substr($d["FirstName"], 0, 1) . $d["LastName"]);
        $d["Email"] = $d["FirstName"] . $d["LastName"] . "@vcsschools.org";
        $d["Password"] = "love";
        $n = count($WOIPTeams);
        $d["IPT"] = $WOIPTeams[rand(0, $n - 1)];
        $mentor = false;
        $lead   = false;
        $member = true;
        $active = true;
        if(rand(0,9) >= 9) $mentor = true;
        if(!$mentor && rand(0,9) >= 8) $lead = true;
        if(rand(0,20) > 19) $active = false; 
        $d["Tags"] = "Member/Worker";
        if($lead) $d["Tags"] .= "/IPTLead";
        if($mentor) $d["Tags"] .= "/Mentor";
        $d["Active"] = $active;
        $msg = CreateNewUser($d);
        if($msg === true) $nsuccess++;
        //else echo 'Create Fail.';
    }
    return "Number of Accounts Generated: " . $nsuccess;

}


// --------------------------------------------------------------------
// Returns an array of random names.
function random_names()
{
    $randnames = array(
    "Brenna"   ,"Neese",
    "Donna"    ,"Harkness",
    "Renda"    ,"Lindley",
    "Brigitte" ,"Brose",
    "Regena"   ,"Halsted",
    "Andrea"   ,"Blust",
    "Nisha"    ,"Eldreth",
    "Michaele" ,"Ellis",
    "Carmina"  ,"Gilyard",
    "Eloy"     ,"Woody",
    "Temeka"   ,"Rapoza",
    "Carolee"  ,"Reynosa",
    "Florrie"  ,"Mooneyham",
    "Claudio"  ,"Mcfaddin",
    "John"     ,"Dogan",
    "Magnolia" ,"Finnerty",
    "Vita"     ,"Serrata",
    "Rosio"    ,"Teller",
    "Leonard"  ,"Hauk",
    "Tomiko"   ,"Matley",
    "Kala"     ,"Stemple",
    "Caprice"  ,"Cottrell",
    "Gracia"   ,"Abrahamson",
    "Tiffani"  ,"Syring",
    "Lara"     ,"Wicker",
    "Cira"     ,"Carta",
    "Marlon"   ,"Talley",
    "Coleen"   ,"Fidler",
    "Arthur"   ,"Pearse",
    "Shizue"   ,"Jorden",
    "Chaya"    ,"Saladin",
    "Reinaldo" ,"Maly",
    "Caroll"   ,"Martino",
    "Hisako"   ,"Korus",
    "Jasmin"   ,"Truex",
    "Denver"   ,"Haith",
    "Justin"   ,"Oyer",
    "Zula"     ,"Philbrook",
    "Annemarie","Oglesby",
    "Emiko"    ,"Sanderlin");
    return $randnames;
}



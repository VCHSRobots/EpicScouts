<?php
// --------------------------------------------------------------------
// welcome.php -- The main welcome page.  Come here directly after
//                a login.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();

$welcome_text = "<h2>Welcome to Epic Robotz WORK ORDER System!</h2>";
$ins_file = "../docs/Welcome.md";
if(file_exists($ins_file)) { $welcome_text = MarkdownToHtml(file_get_contents($ins_file), "welcome_md"); }

$stylesheet=array("../css/global.css", "../css/nav.css", "../css/welcome.css");
include "../forms/header.php";
include "../forms/nav_form.php";
include "../forms/welcome_form.php";
include "../forms/footer.php";

?>
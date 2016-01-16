<?php
// --------------------------------------------------------------------
// utils.php -- Utility area of the web site.
//
// Created: 01/11/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();

$pagetitle = "Utilities To Manage Work Orders";
$instructions = "";
$ins_file = "../docs/Utilities.md";
if(file_exists($ins_file)) { $instructions = MarkdownToHtml(file_get_contents($ins_file), "utils_md"); }
if(!empty($instructions)) $pagetitle = "";
$doform = true;

$stylesheet=array("../css/global.css", "../css/nav.css", "../css/utils.css");
include "../forms/header.php";
include "../forms/nav_form.php";
include "../forms/utils_menubar.php";
include "../forms/utils_form.php";
include "../forms/footer.php";

?>
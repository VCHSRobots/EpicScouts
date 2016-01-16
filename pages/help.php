<?php
// --------------------------------------------------------------------
// help.php -- Shows Help inside the web site.
//
// Created: 01/04/16 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

session_start();
log_page();
CheckLogin();
$help_file = "../docs/help.md";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($help_file)) 
{ 
	$pagetitle = "";
	$markdown = file_get_contents($help_file); 
	$pagecontent = MarkDownToHtml($markdown);
}
else 
{
	$pagetitle = "Help and Support";
	$pagecontent = "Sorry, help is not avaliable.";
}

GenerateHTML:
include "forms/header.php";
include "forms/nav_form.php";
include "forms/null_form.php";
include "forms/footer.php";

?>
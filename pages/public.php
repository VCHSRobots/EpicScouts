<?php
// --------------------------------------------------------------------
// public.php -- Impements the page for the publc.
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);
$timer = new timer();

$public_file = "../docs/publichelp.md";
$error_msg = "";
$success_msg = "";
$instructions = "";

if(file_exists($public_file)) 
{ 
    $pagetitle = "";
    $markdown = file_get_contents($public_file); 
    $pagecontent = MarkDownToHtml($markdown); 
}
else 
{
    $pagetitle = "";
    $pagecontent = "Sorry, there is no help to answer questions at this time.";
}

GenerateHTML:
$stylesheet=array("../css/global.css", "../css/public.css");
include "forms/header.php";
include "forms/public_form.php";
include "forms/footer.php";

?>
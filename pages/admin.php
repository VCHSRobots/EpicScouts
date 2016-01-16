<?php
// --------------------------------------------------------------------
// admin.php -- The main admin page.  Come here on "admin" in nav menu.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();

/*
$menubar = array( 
	array( "caption" => "List Users",    "href" => "admin_listusers.php"),
	array( "caption" => "Add User",      "href" => "admin_adduser.php"),
	array( "caption" => "Upload Users",  "href" => "admin_uploadusers.php"),
	array( "caption" => "Show Log",      "href" => "admin_showlog.php"),
	array( "caption" => "Masquerade",    "href" => "admin_masquerade.php"));
*/

include "forms/header.php";
include "forms/nav_form.php";
include "forms/admin_menubar.php";
echo '<div class="content_area">';
echo '<h2>Administration for This Website</h2>';
echo '<p>Use the links above for various admin tasks.</p>';
echo '</div>';
include "forms/footer.php";

?>
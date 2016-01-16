<?php
// --------------------------------------------------------------------
// null.php -- Not Implemented Feature
//
// Created: 12/30/14 DLB
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();

include "forms/header.php";
include "forms/nav_form.php";
echo '<div class="content_area">';
echo '<h2>Feature Not Implemented</h2>';
echo '<p>Sorry, this feature is not implemented yet.</p>';
echo '</div>';
include "forms/footer.php";

?>
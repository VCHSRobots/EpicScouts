<?php
// --------------------------------------------------------------------
// keepout_form.php -- HTML fragment to show the not-logged-in form.
//
// Created: 12/31/15 DLB
// --------------------------------------------------------------------

ob_clean();
include "forms/header.php";
echo '<div style="margin-top: 40px; margin-left: 120px;">' . "\n";
echo '<p class="msg_background">You are NOT logged in.</p>' . "\n";
echo '</div>' . "\n";
echo '<div class="btn_backgnd_div" style="margin-left: 125px; margin-top: 40px;">' . "\n";
echo '<a class="btn_backgnd" href="login.php">Login Page</a>' ."\n";
echo '</div>' . "\n";
include "forms/footer.php";

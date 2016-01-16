<?php
// --------------------------------------------------------------------
// public_form.php -- HTML fragment for info for non-logged in visitor.
//
// Created: 01/06/16 DLB
// --------------------------------------------------------------------


echo '<div id="public_form_block" style="min-height: 600px;">' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

if(isset($pagecontent))
{
	if(!empty($pagecontent))
	{
		echo '<div id="pagecontent">' . $pagecontent . '</div>' . "\n";
	}
}

echo '<div id="public_login_link"><a href="login.php">Back to Login</a></div>' . "\n";

echo '</div>' . "\n";

?>
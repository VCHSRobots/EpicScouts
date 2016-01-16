<?php
// --------------------------------------------------------------------
// admin_edituser_form.php -- HTML fragment to show the edit_user form.
//
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<div class="page_title">' . $pagetitle . '</div>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

if(!empty($doform))
{
	echo '<span style="font-size: 12pt; color: #999999;">UserID:</span>';
	echo '<span style="font-size: 14pt; color: black; font-weight: bold;">' . $userid . '</span>';
	echo '<span style="font-size: 12pt; color: #999999; margin-left: 20px;">UserName:</span>';
	echo '<span style="font-size: 14pt; color: black; font-weight: bold;">' . $username . '</span>';

	echo '<div class="inputform_area">' . "\n";
	echo '<form action="admin_edituser.php" method="post">' . "\n";

	RenderParams($param_list, "admin_edituser_");

	echo '<div style="clear: both;"></div>' . "\n";

	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Submit">' . "\n";
	echo '</div>';
	echo '</form></div>' . "\n";

	if(!empty($instructions))
	{
		echo '<div class="inputfrom_instructions">' . "\n";
		echo $instructions;
		echo '</div>' . "\n"; 
	}

	echo '<br>Leave password blank to keep current one.'; 
}
echo '</div' . "\n";
?>
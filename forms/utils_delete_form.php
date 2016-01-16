<?php
// --------------------------------------------------------------------
// utils_delete_form.php -- UI for WO Delete
//
// Created: 01/07/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

if($doform)
{
	echo '<div id="utils_delete_form" class="inputform_area">' . "\n";
	echo '<form action="utils_delete.php" method="post">' . "\n";
	RenderParams($param_list, "utils_delete_");
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
}
echo '</div' . "\n";
?>
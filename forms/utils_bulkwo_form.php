<?php
// --------------------------------------------------------------------
// utils_bulkwo_form.php -- Form to generate bulk workorders.
//
// Created: 01/06/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">' . "\n";

echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";

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
	echo '<div class="inputform_area">' . "\n";
	echo '<form action="utils_bulkwo.php" method="post">' . "\n";
	RenderParams($param_list, "utils_bulkwo_");
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
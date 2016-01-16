<?php
// --------------------------------------------------------------------
// wo_new_form.php -- HTML fragment to show new workorder form.
//
// Created: 12/31/15 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">New Work Order</h2>' . "\n";

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
	echo '<form action="wo_new.php" method="post">' . "\n";
	RenderParams($param_list, "wo_new_");
	echo '<div style="clear: both;"></div>' . "\n";
	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Submit">' . "\n";
	echo '</div>';
	echo '</form></div>' . "\n";
}

if(!empty($link_to_view))
{
	echo '<div id="view_button"><a class="btn_form_submit" href="' . $link_to_view . '">View Work Order</a></div>';
}

if(!empty($instructions))
{
	echo '<div class="inputfrom_instructions">' . "\n";
	echo $instructions;
	echo '</div>' . "\n"; 
}
echo '</div' . "\n";
?>
<?php
// --------------------------------------------------------------------
// wo_edit_form.php -- Allows editing on WO.
//
// Created: 01/04/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">' . "\n";

echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";
if(!empty($wo))
{
	RenderField("wo_top", "wo_WIDStr",        "",                     $wo["WIDStr"]                );
	echo '<div style="clear: both;"></div>' . "\n";
	//RenderField("wo_top", "wo_Title",         "Title:",               $wo["Title"]                 );
	//echo '<div style="clear: both;"></div>' . "\n";
}

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
	echo '<form action="wo_edit.php" method="post">' . "\n";
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
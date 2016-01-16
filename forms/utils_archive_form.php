<?php
// --------------------------------------------------------------------
// utils_archive_form.php -- UI for WO archive
//
// Created: 01/11/16 DLB
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

	echo '<div id="utils_archive_top" class="inputform_area">' . "\n";
	echo '<form action="utils_archive.php" method="post">' . "\n";
	render_text_field("Work Order to Archive", "WIDArchive", "", "utils_archive", $widarchivestr);
	echo '<div style="clear: both;"></div>' . "\n";
	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Submit" name="Archive">' . "\n";
	echo '</div></form></div>';

	echo '<div id="utils_archive_bot" class="inputform_area">' . "\n";
	echo '<form action="utils_archive.php" method="post">' . "\n";
	render_text_field("Work Order to Resurrect", "WIDUnArchive", "", "utils_unarchive", $widunarchivestr);
	echo '<div style="clear: both;"></div>' . "\n";
	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Submit" name="UnArchive">' . "\n";
	echo '</div></form></div>';

	if(!empty($instructions))
	{
		echo '<div class="inputfrom_instructions">' . "\n";
		echo $instructions;
		echo '</div>' . "\n"; 
	}
}
echo '</div' . "\n";
?>
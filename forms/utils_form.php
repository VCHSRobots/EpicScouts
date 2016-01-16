<?php
// --------------------------------------------------------------------
// utils_form.php -- Form for utilites.
//
// Created: 01/11/16 DLB
// --------------------------------------------------------------------

echo '<div class="content_area">' . "\n";

if(!empty($pagetitle)) echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";

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

	if(!empty($instructions))
	{
		echo '<div class="inputfrom_instructions">' . "\n";
		echo $instructions;
		echo '</div>' . "\n"; 
	}
}
echo '</div' . "\n";
?>
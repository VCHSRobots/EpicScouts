<?php
// --------------------------------------------------------------------
// wo_add_data_form.php -- F0rm for appending data or pic to a WO.
//
// Created: 01/03/16 DLB
// --------------------------------------------------------------------


echo '<div class="content_area">' . "\n";
echo '<div class="page_title"> ' . $pagetitle . '</div>' . "\n";

if(!empty($wo))
{
	RenderField("wo_top", "wo_WIDStr",        "",                     $wo["WIDStr"]                );
	echo '<div style="clear: both;"></div>' . "\n";
	RenderField("wo_top", "wo_Title",         "Title:",               $wo["Title"]                 );
	echo '<div style="clear: both;"></div>' . "\n";
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
	echo '<form action="wo_add_data.php" method="post" enctype="multipart/form-data">' . "\n";
	RenderParams($param_list, "wo_add_data_");
	echo '<div style="clear: both;"></div>' . "\n";
	echo '<div class="btn_form_submit_div">';
	echo '<input class="btn_form_submit" type="submit" value="Submit">' . "\n";
	echo '</div>';
	echo '</form></div>' . "\n";
}
if(!empty($instructions)) 
{ 
    echo '<div class="instructions"><pre>';
    echo $instructions;
    echo '</pre></div>';
}

echo '</div>' ."\n";

?>
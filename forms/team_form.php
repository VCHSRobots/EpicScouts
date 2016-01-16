<?php
// --------------------------------------------------------------------
// team_form.php -- Displays info for entire team.
//
// Created: 01/04/16 DLB
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

echo '<div id="main_stats_block">' . "\n";
RenderField("team", "Opened",  "Opened:", $nOpened);
RenderField("team", "Urgent",  "Urgent:", $nUrgent);
RenderField("team", "Closed",  "Closed:", $nClosed);
echo '</div>' . "\n";
echo '<div style="clear: both;"></div>' . "\n";

if(!empty($tabledata) && !empty($tableheader))
{
	RenderTable($tableheader, $tabledata, "team");
}

if(!empty($instructions))
{
	echo '<div class="inputfrom_instructions">' . "\n";
	echo $instructions;
	echo '</div>' . "\n"; 
}
echo '</div' . "\n";
?>
<?php
// --------------------------------------------------------------------
// inbox_form.php -- HTML fragment to for Inbox page.
//
// Created: 01/04/16 SS
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

if(!empty($IPT))
{
	echo '<div id="inbox_team_name_block">' . "\n";
	echo '<div id="inbox_team_name_label">IPT Name:</div>' . "\n";
	echo '<div id="inbox_team_name_value">' . $IPT . '</div>' . "\n";
	echo '<div style="clear: both"></div>' . "\n";
	echo '</div>' . "\n";
	
	if(!empty($pagetext))  echo '<p>' . $pagetext . '</p>' . "\n";
	if(!empty($limittext)) echo '<p>' . $limittext . '</p>' . "\n";
	
	if(!empty($tabledata) && !empty($tableheader))
	{
		RenderTable($tableheader, $tabledata, "inbox_submit");
	}
	if ($sql != "" && $result->num_rows >0) 
	{
		include "forms/statuskey_form.php";
	}
}
else
{
	echo '<div id="inbox_noteam">Error: This Inbox is not associated with a team.  ' . "\n";
	echo 'Are you somehow special?  Like are you an admin or editor without a IPT team assignment?</div>' . "\n";
}

echo '</div' . "\n";
?>
<?php
// --------------------------------------------------------------------
// utils_list_archived_form.php -- HTML fragment to list archvied WOs
//
// Created: 01/11/16 DLB
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

if(!empty($pagetext))
{
	echo '<div id="pagetext">' . "\n";
	echo $pagetext;
	echo '</div>' . "\n";
}

if(!empty($limittext))
{
	echo '<p>' . "\n";
	echo $limittext;
	echo '</p>' . "\n";
}

if(!empty($tabledata) && !empty($tableheader))
{
	RenderTable($tableheader, $tabledata, "archived_list");
}

include "forms/statuskey_form.php";

echo '</div' . "\n";
?>
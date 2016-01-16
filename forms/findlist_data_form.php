<?php
// --------------------------------------------------------------------
// findlist_data_form.php -- HTML fragment to for findlist page.
//
// Created: 01/04/16 SS
// --------------------------------------------------------------------

echo '<div style="clear: both; margin-top: 20px; margin-bottom: 10px;">' . "\n";
echo '<p>' . $pagetext . '</p>';
echo '</div>' . "\n";

if(!empty($limittext)) 
{
	echo '<div style="clear: both;"></div>' . "\n";
	echo '<p>' . $limittext . '</p>' . "\n";
}

if(!empty($tabledata) && !empty($tableheader))
{
	
	RenderTable($tableheader, $tabledata, "findlist_submit");
}
if ($sql!= "" && $view == "full" && $isResult) 
{
	include "forms/statuskey_form.php";
}

echo '</div' . "\n";
?>
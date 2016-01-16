<?php
// --------------------------------------------------------------------
// team_ipt_form.php -- HTML fragment to for your work page.
//
// Created: 01/04/16 DLB
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

RenderField("", "teamname"  ,  "IPT Team: ", $teamname);
echo '<div style="clear: both"></div>' . "\n";
RenderField("", "searchtype",  "",           $searchtitle);
echo '<div style="clear: both"></div>' . "\n";

if(!empty($limittext))
{
	echo '<p>' . $limittext . '</p>' . "\n";
}
 
if(!empty($tabledata) && !empty($tableheader))
{
	RenderTable($tableheader, $tabledata, "team_ipt");
}
else
{
	echo '<div id="none">None Found.</div>' . "\n";
}

echo '</div' . "\n";
?>
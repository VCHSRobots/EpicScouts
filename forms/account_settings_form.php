<?php
// --------------------------------------------------------------------
// account_settings_form.php -- HTML fragment to show the user setup form.
//
// Created: 12/30/14 DLB
// Updated: 12/31/15 DLB -- Hacked for WO system.
// --------------------------------------------------------------------

echo '<div class="content_area">';
echo '<h2 class="page_title">Account Settings for ' . $username . '</h2>' . "\n";

echo '<div class="members_toparea">';
if(!empty($picurl))
{
    echo '<div class="members_pic_div">';
    echo '<div class="members_pic"><img src="' . $picurl . '"></div>';
    echo '</div>';
}

if($havebadge)
{
    echo '<div class="members_showbadge_picarea">';
    echo '<img class="members_showbadge_badge" src="' . $badge_front_url . '">';
    echo '</div>';             

    echo '<div class="members_showbadge_picarea">';
    echo '<img class="members_showbadge_badge" src="' . $badge_back_url . '">';
    echo '</div>';   
}

echo '</div>';

echo '<div style="clear: both;"></div>';


if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

//echo '<div class="members_paramlabel">UserID:</div>';
//echo '<div class="members_paramvalue">' . $userid . '</div>';

//echo '<div class="members_paramlabel">UserName:</div>';
//echo '<div class="members_paramvalue">' . $username . '</div>';

echo '<div class="inputform_area">' . "\n";
echo '<form action="account_settings.php" method="post">' . "\n";

RenderParams($param_list, "account_settings_");

echo '<div style="clear: both;"></div>' . "\n";

echo '<div class="btn_form_submit_div">';
echo '<input class="btn_form_submit" type="submit" value="Submit">' . "\n";
echo '</div>';
echo '</form></div>' . "\n";

echo '<div id="account_settings_instructions" class="inputfrom_instructions">' . "\n";
echo '<p>Leave passwords blank to keep current one.</p>';
/*
echo '<p>NOTE: IPT team preference is to aid in your use of this web site. ';
echo 'Your actual IPTeam assignment may be different than what you set here. </p>';
*/
echo '</div>' . "\n"; 

echo '</div' . "\n";
?>
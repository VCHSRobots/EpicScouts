<?php
// --------------------------------------------------------------------
// display_image_form.php -- Form to display uploaded image.
//
// Created: 01/03/16 DLB
// --------------------------------------------------------------------


echo '<div class="content_area">' . "\n";

echo '<h2 class="page_title"> ' . $pagetitle . '</h2>' . "\n";

if(!empty($success_msg))
{
    echo '<div class="inputform_msg" id="inputform_success_msg" >' . $success_msg . "</div>";
}
if(!empty($error_msg))
{
    echo '<div class="inputform_msg" id="inputform_error_msg" >' . $error_msg . "</div>";
}

RenderField("wo", "wo_WIDStr",        "",                     $wo["WIDStr"]                );
echo '<div style="clear: both;"></div>' . "\n";

if(!empty($pagetext)) echo $pagetext;
RenderField("wo", "wo_Title",         "Title:",               $wo["Title"]                 );

echo '<div style="clear: both;"></div>' . "\n";

if(!empty($picurl))
{
	echo '<div id="di_image_link"><a class="btn_form_submit" href="' . $picurl . '" target="_blank">Get Original</a></div>' . "\n";
}

echo '<div id="di_image_block">' . "\n";
if(!empty($piccaption)) 
{
	echo '<div id="di_textcaption">' . $piccaption . '</div>' . "\n";
}
if(!empty($picurl))
{
	echo '<div id="di_image"><img src="' . $picurl . '"></div>' . "\n";
}
echo '</div>' . "\n";

if(!empty($instructions)) 
{ 
    echo '<div class="instructions"><pre>';
    echo $instructions;
    echo '</pre></div>';
}

echo '</div>' ."\n";

?>
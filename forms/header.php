<?php
// --------------------------------------------------------------------
// header.php -- header for the Epic Admin website.
// 
// Created: 12/29/14 DLB
// --------------------------------------------------------------------

// Notes:
// 1. To use this header, include 'forms/header.php' and 'forms/footer.php'
// 2. Put the content between header and the footer.
// 3. The default width of the pages is 960 pixels -- sligthly less than with
//    proper width for an ipad. This can be overriden by setting $browser_width
//    to a different value before including this file.  Don't go less than 500.
// 4. The default style sheet is 'css/global.css'.  This can be overriden
//    by setting $stylesheet to a different css file, or an array of
//    css files.  If overrriden, 'css/global.css' is not automatically
//    included.
// 5. To get the navigation bar on the left, include 'forms/navform.php'
//    immediately after this file.  
// 6. If the nav bar is included, then it is best to enclose all other
//    content inside a <div class="content"> ... </div> to put the content
//    in the right place.  This script will automatically size the 
//    content width to fit for different $browser_width settings.

require_once "maindef.php";

?>

<!DOCTYPE html>
 
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <?php
        if(isset($pagetabtitle))
        {
            echo '<title>' . $pagetabtitle . '</title>' . "\n";
        }
        else 
        {
            echo '<title>EPIC Work Order</title>' . "\n";
        }
        if(isset($stylesheet)) 
        {
            if(is_array($stylesheet))
            {
                foreach($stylesheet as $sheet) 
                {
                    echo '<link rel="stylesheet" type="text/css" href="' . $sheet . '">' . "\n";
                }
            }
            else echo '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">' . "\n";
        }
        else
        {
            echo '<link rel="stylesheet" type="text/css" href="../css/global.css">' . "\n";
            echo '<link rel="stylesheet" type="text/css" href="../css/nav.css">' . "\n";
        }

        /*
        // This deals with the Date Picker... 
        echo '<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">' ."\n";
        echo '<script src="//code.jquery.com/jquery-1.10.2.js"></script>' . "\n";
        echo '<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>' . "\n";
        echo '<link rel="stylesheet" href="/resources/demos/style.css">' . "\n";
        echo '<script>' . "\n";
        echo '$(function() { ' . "\n";
        echo '$( "#datepicker" ).datepicker(); ' . "\n";
        echo '});' . "\n";
        echo '</script>' . "\n";
        */
        
        echo '<style>';
        if(!isset($browser_width)) $browser_width = 960;
        echo '#screen_area {width: ' . $browser_width . 'px;}';
        $content_width = $browser_width - 200;
        echo '.content_area {width: ' . $content_width . 'px;}'; 
        echo '.menubar_area {width: ' . $content_width . 'px;}'; 
        echo '</style>' . "\n";
    ?>
</head>
 
<body>
<div id="screen_area">
<div id="header_banner">
    <div id="header_writeable_area">
        <?php
            if(IsLoggedIn()) 
            {
                echo '<div id="header_icon"> <a href="welcome.php"> <img src="../img/epicicon120_new.jpg" height="50px"> </a></div>' . "\n";
                echo '<div id="header_website_name">EPIC Robotz WORK ORDER System</a></div>' . "\n";
                
                echo '<div id="header_account_area">' . "\n"; 

                echo '<div id="header_username_div">' . UserFormattedName();
                if(IsAdmin()) {echo "*";}
                echo '</div>';
                
                echo '<div id="header_accoutmenu_div">';

                  echo '<div class="header_topbutton_div">';
                  echo '<a class="header_topbutton" href="account_settings.php">Settings</a>';
                  echo '</div>';
                
                  echo '<div class="header_topbutton_div">';
                  echo '<a class="header_topbutton" href="logout.php">Logout</a>';
                  echo '</div>';
                
                echo '</div>';
                echo '</div>';
            }
            else {
                echo '<div id="header_icon"> <img src="../img/epicicon120_new.jpg" height="50px"></div>' . "\n";
                echo '<div id="header_website_name">EPIC Robotz WORK ORDER System</div>' . "\n";
            }
        ?>
    </div>
 </div>
 <div style="clear: both;"></div>
 <div id="middle_area">

 
<?php
// --------------------------------------------------------------------
// weblib:  library fucntions to handle website-wide functions.
//
// Created: 12/13/14 DLB
// --------------------------------------------------------------------

require_once "config.php";
require_once "loglib.php";

// --------------------------------------------------------------------
// Puts out a complete page, with the given error message, and then
// exits.  The error message can be either a simple string, or an
// array of strings.  This routine also writes the error message
// to the log file.  This function should be called BEFORE any output
// is written in response.
function DieWithMsg($loc, $e)
{
    log_error($loc, $e);
    
    ob_clean();
    include "forms/header.php";
    echo '<h2 class="page_title">Major Error Alert</h2>' . "\n";
    echo '<div class="diemsg">';
    echo '<p>You have arrived at this page because some unexpected error happened that probably was';
    echo ' not your fault.  It might be due to bad programming, or something that changed deep inside the ';
    echo 'system.  This error has been logged for futher review.  The info about the error is below. ';
    echo 'You should try again from the main menu.  Sorry. </p>';
    echo '</div>';
    echo "\n";
    echo '<p>Reporting File: ' . $loc . "</p>\n";
    if(is_array($e))
    {
        foreach($e as $t) { echo '<p>' . $t . "</p><br>\n"; }
    }
    else { echo '<p>' . $e . "</p><br>\n"; }
    echo '<div style="margin-top: 30px; margin-left: 100px;">' . "\n";
    echo '<a class="btn_backgnd" href="welcome.php">Go Back to Start</a>' ."\n";
    echo '</div' . "\n";
    echo "<br><br>";
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Dies due to SQL querry...
function DieWithBadSql($loc, $sql)
{
    DieWithMsg($loc, array("SQL Querry Failure!", "SQL=" . $sql));
}

// --------------------------------------------------------------------
// Puts out a complete page, with only the given error message, and 
// not the long explanation, and then exits.  The error message can
// be either a simple string, or an array of strings.  This routine
// also writes the error message to the log file.  Note, this function
// should be called BEFORE any other output is written in response.
function DieNice($loc, $e)
{
    log_error($loc, $e);
    
    ob_clean();
    include "forms/header.php";
    echo '<h2 class="page_title">Error Alert...</h2>' . "\n";
    echo '<div class="diemsg">' . "\n";
    if(is_array($e))
    {
        foreach($e as $t) { echo '<p>' . $t . "</p><br>\n"; }
    }
    else { echo '<p>' . $e . "</p><br>\n"; }
    echo "</div>";

    echo '<div style="margin-top: 30px; margin-left: 100px;">' . "\n";
    echo '<a class="btn_backgnd" href="welcome.php">Go Back to Start</a>' ."\n";
    echo '</div' . "\n";
    echo "<br><br>";
    include "forms/footer.php";
    exit;
}

// --------------------------------------------------------------------
// Jumps to another page by re-direction.  The $pagefile is the base
// name of the new page to load, relative to the current page of the
// website.  $args is either null or an associtive array of arguments
// to pass to the page.  This function should be called
// before ANY output is writen to the response.
//
// NOTE: the arguments, if used, must be super simple, and not contain
// any weird characters or spaces.  This is because we do not rely
// on the pecl_http extension.
//
// This function does not return to the caller.
function JumpToPage($pagefile, $args = null)
{
    global $config;
    $path = $config["BaseUrl"] . $pagefile;
    if(isset($args))
    {
        $first = true;
        foreach($args as $key => $value)
        {
            if($first) { $path = $path . '?'; $first = false; }
            else {$path = $path. '&'; }
            $path = $path . $key . "=" . $value;
        }
    }
 
    header("Location: " . $path);
    exit;
}

// --------------------------------------------------------------------
// Checks to see if it is likely that the user tried to upload a 
// file.  Input is the array that comes from $_FILE.  True is returned
// if it appears that an attempt was made to upload a file.
function CheckFileInput($info)
{
    if(empty($info["name"]) && empty($info["type"])) return false;
    return true;
}


?>
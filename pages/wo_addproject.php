<?php
// --------------------------------------------------------------------
// wo_addproject.php -- 
//
// Created: ??
// --------------------------------------------------------------------

require_once "../maindef.php";
$loc = rmabs(__FILE__);

session_start();
log_page();
CheckLogin();
CheckAdmin();
$timer = new timer();
$error_msg = "";
$success_msg = "";

$param_list = array(
array("FieldName" => "ProjectName",  "FieldType" => "Text"),
array("FieldName" => "ProjectDescription",  "FieldType" => "Text")
);

if( $_SERVER["REQUEST_METHOD"] == "POST")
{
    PopulateParamList($param_list, $_POST);

    // Check for illegal input...
    if(!IsSqlTextOkay($_POST))
    {
        $error_msg = "Illegal characters in input... Do not use quotes and control chars.";
        goto GenerateHtml;
    }

    // Check for required inputs:
    $sEmpty = array();
    if(empty($_POST["ProjectName"]))  $sEmpty[] = "Project Name";
    if(empty($_POST["ProjectDescription"]))  $sEmpty[] = "Project Description";
    if(count($sEmpty) > 0)
    {
        $error_msg = "Required information missing: ";
        $c = 0;
        foreach($sEmpty as $s) 
        {
            if($c > 0) $error_msg .= ', ';
            $error_msg .= $s;
            $c++;
        }
        $error_msg .= '.';
        goto GenerateHtml;
    }
    
    if(empty($_POST["ProjectName"]) ) 
    {
        $error_msg = "Error: ProjectName cannot be blank.";
        goto GenerateHtml;
    }
	

    $data = ExtractValuesFromParamList($param_list);
    $okay = CreateNewUser($data);
    if($okay === true)
    {
        $success_msg = 'User "' . $_POST["ProjectName"] . '" successfully added.';
        foreach($param_list as &$param_spec) { unset($param_spec["Value"]); }
    }
    else
    {
        $error_msg = $okay;
    }
}

// Render the page based on state variables that were set above...
// These are: $error_msg, $success_msg, $param_list.

GenerateHtml:
include "forms/header.php";
include "forms/nav_form.php";
include "forms/wo_menubar.php";
//include "forms/wo_addproject_form.php";
include "forms/footer.php";
?>
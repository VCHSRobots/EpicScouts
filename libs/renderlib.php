<?php
// --------------------------------------------------------------------
// renderlib.php -- code for rendering html according to a "spec".
//
// Created: 12/08/14 DLB
// Updated: 12/29/14 DLB -- Added FieldType "Password".
// Updated: 01/03/16 DLB -- Lots of improvements, including tables.
// --------------------------------------------------------------------

// This code deals with items called "Parameter Specifications", or 
// param_spec for short.  A param_spec is a way to virtualize the idea of
// a field and it's value.  Normally, on HTML forms, a field has a label
// (caption) and and a place to enter data. Upon user submission of the form
// the data is stored in a database table, under a column that has a name.
// The column name is also known as the "field name".
//
// Instead of hardcoding all the fields for a database table in HTML script,
// a param_spec offers a way to configure the fields outside of the script, and
// then an "engine" is used to render all the fields when the page is loaded.
// That way, the fields can be changed using a configuration file,
// instead of re-coding the page.
//
// The code in this file is used to define how param_specs work, and offer
// utility functions that work with param_specs.  A param_spec is simply an
// an associtive array that defines a parameter.  Some keys in the array
// are required, and other keys are optional.  The keys for an param_spec
// are as follows:
//
//   FieldName    -- Required. The database name for the field.
//   FieldType    -- Required. One of: 'Text', 'Selection', 'Boolean',
//                   'Password', 'TextArea', 'Date', 'File', 'Hidden'.
//   Selection    -- Required if FieldType=>'Selection'. An array of
//                   possible answers (text only)
//   Caption      -- Optional. The label to use for the field.  If not found
//                   FieldName is used instead.
//   Instructions -- Optional. Short instructions that might be displayed by
//                   javascript someday.
//   Style        -- Optional. Special css style script that is embedded with
//                   the input element. Note, that styles placed here will
//                   override the CSS from other places. This code should NOT
//                   have any embedded quotes.
//   Rows         -- Optional. Number of rows to use for a TextArea. Ignored
//                   for other field types.  (Default is 3).
//   Columns      -- Optional. Number of columns to use for a TextArea. 
//                   Overrides style settings that make a nice layout -- so
//                   beware.  Ignored for other field types.
//   Value        -- Optional. The Value of parameter.  Used to set current
//                   or default value of the field.  If not found, the field
//                   will be empty when rendered.
//
// Param_specs can be grouped into an array that is called a "Parameter List" 
// or param_list. This file also provides functions that deal with such lists. 
// For example,  given a param_list and a FieldName, a value can be set or retrieved.  
//
// Public API
// ==========
// RenderParamList() -- Render param_list into an HTML form.
// RenderParamSpec() -- Renders one param_spec into an HTML form.
// PostPopulate()    -- Uses the values in $_POST to populate the values in a param_list.
// SetValueInParamList() -- Sets a value in a param_list.
// GetValueFromParamList() -- Retrieves a value from a param_list.
//                   

// --------------------------------------------------------------------
// Renters a param_list as html into the current output stream.  
function RenderParams($param_list, $divname="")
{
    foreach($param_list as $param_spec)
    {
        $id = "";
        if(!empty($divname)) 
        {
            $id = $divname . $param_spec["FieldName"];
            echo '<div id="' . $id .'">' . "\n";
        }
        RenderParamSpec($param_spec, $id);
        if(!empty($divname)) echo '</div>' . "\n";
    }
}

// --------------------------------------------------------------------
// Renders the parameter_spec as html into the current output stream.  Errors in the spec
// are logged, but not actknowleged to the user.  Instead the function simply returns.
// If the $param_spec has a "Value" key, then the value is rendered in the parameter.
function RenderParamSpec($param_spec, $divname="")
{
    $current_val = null;
    if(array_key_exists("Value", $param_spec))
    {
        $current_val = $param_spec["Value"];
    }

    if(!array_key_exists("FieldName", $param_spec) || !array_key_exists("FieldType", $param_spec))
    {
        log_error("renderlib.php->RenderParamInput", 'Bad param_spec: no FieldName or FieldType.');
        return;
    }
    $fn = $param_spec["FieldName"];
    $ft = $param_spec["FieldType"];
    if($ft == "Selection")
    {
        if(!isset($param_spec["Selection"])) 
        {
            log_error("renderlib.php->RenderParamInput", 'Bad param_spec for ' . $fn . ": No selection element.");
            return;
        }
        $sel = $param_spec["Selection"];
        if(!is_array($sel))
        {
            log_error("renderlib.php->RenderParamInput", 'Bad param_spec for ' . $fn . ": Selection element is not an array.");
            return;
        }
    }
    if(!in_array($ft, array("Text", "Password", "Boolean", "Selection", "TextArea", "Date", "File", "Hidden")))
    {
        log_error("renderlib.php->RenderParamInput", 'Bad param_spec for ' . $fn . '": FieldType "' . $ft . '" not valid."');
        return;
    }
    
    if(isset($param_spec["Caption"]))      { $cap = $param_spec["Caption"]; }
    else                                   { $cap = $fn; }
    if(isset($param_spec["Instructions"])) { $ins = $param_spec["Instructions"]; }
    else                                   { $ins = ""; }
    if(isset($param_spec["Style"]))        { $style = $param_spec["Style"]; }
    else                                   { $style = ""; }
    if(isset($param_spec["Rows"]))         { $rows = intval($param_spec["Rows"]); }
    else                                   { $rows = 3; }
    if(isset($param_spec["Columns"]))      { $columns = intval($param_spec["Columns"]); }
    else                                   { $columns = ""; }
    
    if($ft == "Text") 
    {
        render_text_field($cap, $fn, $style, $divname, $current_val);
        return;
    }
    
    if($ft == "Password")
    {
        render_password_field($cap, $fn, $style, $divname, $current_val);
        return;
    }
 
    if($ft == "Boolean")
    {
        render_boolean_field($cap, $fn, $divname, $current_val);
        return;
    }
 
    if($ft == "Selection")
    {
        render_selection_field($cap, $fn, $sel, $style, $divname, $current_val);
        return;
    }
    
    if($ft == "TextArea")
    {
        render_textarea_field($cap, $fn, $rows, $columns, $style, $divname, $current_val);
        return;
    }

    if($ft == "Date")
    {
        render_date_field($cap, $fn, $style, $divname, $current_val);
        return;
    }

    if($ft == "Hidden")
    {
        render_hidden_field($fn, $divname, $current_val);
        return;
    }

    if($ft == "File")
    {
        render_file_field($cap, $fn, $style, $divname, $current_val);
        return;
    }
    
   log_error("renderlib.php->RenderParamInput", "Should be unreachable code. $ft=" . $ft);
  
}


// --------------------------------------------------------------------
// Poplules a param_list with data from an assoctive array, where
// the keys match the FieldNames.  Normally, upon return, the only
// param_specs with values will be ones that have fieldname matches in
// the data.  If $keepold=true, then this is overriden, and existing 
// values are left alone if they do not have matching data items.
function PopulateParamList(&$param_list, $data, $keepold=false)
{
    foreach($param_list as &$param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if(!array_key_exists("FieldType", $param_spec)) continue;
        $fn = $param_spec["FieldName"];
        $ft = $param_spec["FieldType"];
        if(isset($data[$fn]))
        {
            $param_spec["Value"] = $data[$fn];
        }
        else
        {
            if(!$keepold) unset($param_spec["Value"]);
        }
    }
}

// --------------------------------------------------------------------
// Determines if the given field is in the parameter list.
function IsFieldInParamList($fieldname, $param_list)
{
    foreach($param_list as $param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if($param_spec["FieldName"] == $fieldname) return true;
    }
    return false;
}

// --------------------------------------------------------------------
// Finds the param_spec in a param_list and returns it.  If not
// found, null returned.  The returned param_spec is a copy of the
// one in the list.
function GetParamSpecFromList($fieldname, $param_list)
{
    foreach($param_list as $param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if($param_spec["FieldName"] == $fieldname) return $param_spec;
    }
    return null;
}

// --------------------------------------------------------------------
// Sets a single value in a list of param_specs.  If the parameter
// is found and successfully set, true is returned. False returned 
// otherwise.
function SetValueInParamList(&$param_list, $fieldname, $value)
{
    foreach($param_list as &$param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if(!array_key_exists("FieldType", $param_spec)) continue;
        $fn = $param_spec["FieldName"];
        if($fn != $fieldname) continue;
        $param_spec["Value"] = $value;
        return true;
    }
    return false;
}

// --------------------------------------------------------------------
// Gets a single value out of a list of param_specs.  If the fieldname
// cannot be found, empty is returned.  Or if the fieldname can be found
// but no value is associated with it, empty is also returned.
function GetValueFromParamList($param_list, $fieldname)
{
    foreach($param_list as $param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if(!array_key_exists("FieldType", $param_spec)) continue;
        $fn = $param_spec["FieldName"];
        if($fn != $fieldname) continue;
        if(array_key_exists("Value", $param_spec)) {return $param_spec["Value"]; }
        else {return ""; }
    }
}

// --------------------------------------------------------------------
// Given a parameter list, returns a simple assoctive array where the
// keys are the fieldnames, and the values are extracted from the param_specs
// that have a value key.
function ExtractValuesFromParamList($param_list)
{   
    $data = array();
    foreach($param_list as $param_spec)
    {
        if(!array_key_exists("FieldName", $param_spec)) continue;
        if(!array_key_exists("Value",     $param_spec)) continue;
        $fn = $param_spec["FieldName"];
        $v = $param_spec["Value"];
        $data[$fn] = $v;
    }
    return $data;
}


// --------------------------------------------------------------------
// Render a standard text field in a form.
function render_text_field($caption, $fieldname, $style="", $divname="", $val="")
{
    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock"' . $id[0] . '>' . "\n";
    echo '<div class="inputform_label" ' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_text_field"> <input type="text" ' . $id[2];
    echo '  name="' . $fieldname . '" ';
    if(!empty($style))
    {
        echo 'style="' . $style . '" ';
    }
    if(isset($val) && !empty($val))
    {
        echo 'value="' . $val . '" ';
    }
    echo "\n /></div>\n</div>\n";
}

// --------------------------------------------------------------------
// Render a standard passoword field in a form.
function render_password_field($caption, $fieldname, $style="", $divname="", $val="")
{
    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock" ' . $id[0] . '>' . "\n";
    echo '<div class="inputform_label" ' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_text_field"> <input type="password" ' . $id[2];
    echo '  name="' . $fieldname . '" ';
    if(!empty($style))
    {
        echo 'style="' . $style . '" ';
    }
    if(isset($val) && !empty($val))
    {
        echo 'value="' . $val . '" ';
    }
    echo "\n /></div>\n</div>\n";
}

// --------------------------------------------------------------------
// Render a standard boolean field in a form.
function render_boolean_field($caption, $fieldname, $divname="", $val="")
{
    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_boolean_paramblock"'. $id[0] . '>' . "\n";
    echo '<div class="inputform_label"' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_boolean_area">' . "\n";

    echo '<div class="inputform_boolean_group">  <div class="inputform_boolean_label"> Yes  </div>' . "\n";
    echo '<div class="inputform_boolean_field"> <input type="radio" ' . $id[3] . 'name="' .  $fieldname . '" value="1" ';
    if(isset($val) && $val == true) {echo 'checked="checked" '; }
    echo '/> </div></div>' . "\n";
    
    echo '<div class="inputform_boolean_group">  <div class="inputform_boolean_label"> No  </div>' . "\n";
    echo '<div class="inputform_boolean_field"> <input type="radio" ' . $id[4] . 'name="' . $fieldname . '" value="0" ';
    if(isset($val) && $val == false) {echo 'checked="checked" '; }
    echo '/> </div></div>' . "\n";
    
    echo '<div style="clear: left"></div>' . "\n";

    echo '</div>' . "\n";
    echo '</div>' . "\n";
}

// --------------------------------------------------------------------
// Render a standard selection field in a form.
function render_selection_field($caption, $fieldname, $selections, $style="", $divname="", $val="")
{
    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock" '. $id[0] . '>' . "\n";
    echo '<div class="inputform_label"' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_selection">' . "\n";
    echo '<select ' . $id[2] . ' name="' . $fieldname . '"';
    if(!empty($style))
    {
        echo ' style="' . $style . '"';
    }
    echo '>' . "\n";
    foreach($selections as $s)
    {
        echo '<option name="' . $s , '" value="' . $s . '" ';
        if((isset($val) && !empty($val)) && $val == $s)
        {
            echo 'selected="selected" ';
        }
        echo '> ' . $s . '</option>' . "\n";
    }
    echo '</select>' . "\n";
    echo '</div>' . "\n";
    echo '</div>' . "\n";
}

// --------------------------------------------------------------------
// Render a standard text area field in a form.
function render_textarea_field($caption, $fieldname, $rows, $columns, $style="", $divname="", $val="")
{
    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock" '. $id[0] . '>' . "\n";
    echo '<div class="inputform_label"' . $id[1] .'>' . $caption . " </div>\n";
    echo '<div class="inputform_textarea">' . "\n";
    echo '<textarea ' . $id[2] . ' name="' . $fieldname . '"  rows="' . $rows . '" ';
    if(!empty($columns))
    {
        echo 'cols="' . $columns . '" ';
    }
    if(!empty($style))
    {
        echo 'style="' . $style . '" ';
    }
    echo ">\n";
    if(isset($val) && !empty($val))
    {
        echo $val;
    }
    echo "</textarea>\n</div>\n</div>\n";
}

// --------------------------------------------------------------------
// Render a standard hidden field in a form.
function render_hidden_field($fieldname, $divname="", $val="")
{
    $id = div_id_array($divname);
    echo '<input type="hidden" name="' . $fieldname . '" ' . $id[2] . ' ';
    if(isset($val) && !empty($val))
    {
        echo 'value="' . $val . '" ';
    }
    echo '/>' . "\n";
}

// --------------------------------------------------------------------
// Render a standard date field in a form.
function render_date_field($caption, $fieldname, $style="", $divname="", $val="")
{

    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock"' . $id[0] . '>' . "\n";
    echo '<div class="inputform_label"' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_text_field"> <input type="date" ' . $id[2];
    echo ' name="' . $fieldname . '" ';
    if(!empty($style))
    {
        echo 'style="' . $style . '" ';
    }
    if(isset($val) && !empty($val))
    {
        echo 'value="' . $val . '" ';
    }
    echo "\n /></div>\n</div>\n";
}

// --------------------------------------------------------------------
// Render a standard file field in a form.
function render_file_field($caption, $fieldname, $style="", $divname="", $val="")
{

    $id = div_id_array($divname);
    echo "\n\n";
    echo '<div class="inputform_paramblock"' . $id[0] . '>' . "\n";
    echo '<div class="inputform_label"' . $id[1] . '>' . $caption . " </div>\n";
    echo '<div class="inputform_text_field"> <input type="file" ' . $id[2];
    echo ' name="' . $fieldname . '" ';
    if(!empty($style))
    {
        echo 'style="' . $style . '" ';
    }
    if(isset($val) && !empty($val))
    {
        echo 'value="' . $val . '" ';
    }
    echo "\n /></div>\n</div>\n";
}

// --------------------------------------------------------------------
// Generates div ids for the param spec.  (Helper Func).
function div_id_array($divname)
{
    $id[0] = "";
    $id[1] = "";
    $id[2] = "";
    $id[3] = "";
    $id[4] = "";
    if(!empty($divname))
    {
        $id[0] = ' id="' . $divname . '_block"';
        $id[1] = ' id="' . $divname . '_label"';
        $id[2] = ' id="' . $divname . '_field"';
        $id[3] = ' id="' . $divname . '_yes"';
        $id[4] = ' id="' . $divname . '_no"';
    }
    return $id;
}




?>
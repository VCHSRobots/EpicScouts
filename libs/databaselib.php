<?php
// --------------------------------------------------------------------
// databaselib.php:  library fucntions for database access
//
// Created: 12/03/14 DLB
// --------------------------------------------------------------------

require_once "config.php";

// --------------------------------------------------------------------
// Returns a connection to our database.  Or dies if does not exist...
// Only one connection is held open to the database during client 
// request.  On the first call of a request, the connection is opened.
// Thereafter, a cashed version of the connection is returned.
// Note: the returned connection should "never" be closed while 
// processing a script.  It will automatically be closed by php when
// the script finish and the page is returned to the user.
function GetSqlConnection()
{
    global $config;
    static $conn = null;
    
    if(!is_null($conn)) 
    {
        //echo "Repeat Connection<br>";
        return $conn;
    }
    
    //echo "New Connection<br>";
    $conn = new mysqli(
       $config["db"]["host"], 
       $config["db"]["username"],
       $config["db"]["password"],
       $config["db"]["dbname"]);
   
   if ($conn->connect_error) {
       log_error("databaselib.php->GetSqlConnection", 
          array("Database Connection failed.", $conn->connect_error));
       die("Database Connection failed: " . $conn->connect_error);
   }
   return $conn;
}

// --------------------------------------------------------------------
// Preforms an SQL Query with our local connection. Dies with
// appropriate error messages on error.  The return value is what
// would have been obtained from mysqli::query.
function SqlQuery($loc, $sql)
{
    $conn = GetSqlConnection();
    $result = $conn->query($sql);
    if($result == false) { DieWithBadSql($loc, $sql); }
    return $result;
}

// --------------------------------------------------------------------
// Similar to SqlQuery, but used with prepared statements.  Argments
// can only be strings.  Use this only for insert and update statements.
// Note that non-string values can be encoded directly in the sql before
// this function is called.  Returns the statement that exectued
// which should be closed when done.

function SqlPrepareAndExectue($loc, $sql, $args)
{
    $conn = GetSqlConnection();
    $stmt = $conn->prepare($sql);
    if(!$stmt)
    {
        $msg = array("Sql Prepare Failed.", "sql=" . $sql, $conn->error);
        DieWithMsg($loc, $msg);
    }
    $n = count($args);
    $a = $args;
    if($n > 10) DieWithMsg($loc, "Too many args for Prepare and Execute.");
    if($n ==  0) $r = $stmt->bind_param();
    if($n ==  1) $r = $stmt->bind_param("s",          $a[0]);
    if($n ==  2) $r = $stmt->bind_param("ss",         $a[0], $a[1]);
    if($n ==  3) $r = $stmt->bind_param("sss",        $a[0], $a[1], $a[2]);
    if($n ==  4) $r = $stmt->bind_param("ssss",       $a[0], $a[1], $a[2], $a[3]);
    if($n ==  5) $r = $stmt->bind_param("sssss",      $a[0], $a[1], $a[2], $a[3], $a[4]);
    if($n ==  6) $r = $stmt->bind_param("ssssss",     $a[0], $a[1], $a[2], $a[3], $a[4], $a[5]);
    if($n ==  7) $r = $stmt->bind_param("sssssss",    $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6]);
    if($n ==  8) $r = $stmt->bind_param("ssssssss",   $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7]);
    if($n ==  9) $r = $stmt->bind_param("sssssssss",  $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8]);
    if($n == 10) $r = $stmt->bind_param("ssssssssss", $a[0], $a[1], $a[2], $a[3], $a[4], $a[5], $a[6], $a[7], $a[8], $a[9]);

    if(!$r) DieWithMsg($loc, array("Bind Failure in sql=" . $sql, "NArgs=" . $n));

    $okay = $stmt->execute();
    if($okay === false) { DieWithBadSql($loc, $sql); }
    return $stmt;
}

// --------------------------------------------------------------------
// This function generates part of an Update SQL statement, consisting
// of the SET parameters.  Input is an param_list containting the
// data to be set, and a fields array containing possible field names and
// their formats.  The return is a string that is suitable for
// insertion into an SQL Update statement.  If no data is to be set,
// false is returned.  The param_specs in the param_list must have a
// Value key, or the corrosponding field will not be set.  Also, only
// the fields found in the $field array will be set, not the ones in
// the param_list.  The field array contains arrays of two elements,
// the first being the field name, and the second is the format, which
// can be one of "int", "str", or "bool".  
function GenerateSqlSet_ParamList($param_list, $fields)
{
    // Generate a data set.
    $data = array();
    foreach($param_list as $param_spec)
    {
        if(!isset($param_spec["FieldName"])) continue;
        if(!isset($param_spec["Value"])) continue;
        $fn = $param_spec["FieldName"];
        $v  = $param_spec["Value"];
        $data[$fn] = $v;
    }
    return GenerateSqlSet($data, $fields);
}

// --------------------------------------------------------------------
// This function generates part of an Update SQL statement, consisting
// of the SET parameters.  Input is an associtive array containting the
// data to be set, and a fields array containing possible field names and
// their formats.  The return is a string that is suitable for
// insertion into an SQL Update statement.  If no data is to be set,
// false is returned.  The keys in $data should be named the same
// as the field names or the corrosponding field will not be set.  Also,
// only the fields found in the $field array will be set, not the ones
// in $data.  The $field array contains arrays of two elements,
// the first being the field name, and the second is the format, which
// can be one of "int", "str", or "bool".  
// The output format is " n1=v1, n2=v2, ... ".
function GenerateSqlSet($data, $fields)
{
    $sql = ' ';
    $c = 0;
    foreach($fields as $f)
    {
        $fn = $f[0];  // Field name
        $ft = $f[1];  // Field type
        if(!isset($data[$fn])) continue;
        if(is_null($data[$fn])) continue;
        $v = $data[$fn];
        if($c != 0) $sql .= ', ';
        $sql .= $fn . '=';
        if($ft == 'int')       { $sql .= intval($v);     }
        else if($ft == 'str')  { $sql .= '"' . SqlClean($v) . '"'; }
        else if($ft == 'bool') { $sql .= TFstr($v);      }
        else {DieWithMsg("databaselib.php->GenerateSQLSet", "Bad type: " . $ft . " for field " . $fn . '.'); }
        $c++;
    }
    if($c == 0) return false;
    return $sql;
}

// --------------------------------------------------------------------
// This function generates part of an SQL Insert statement, consisting
// of the column names and the VALUE parameters.  Input is an associtive
// array containting the data to be inserted, and a fields array
// containing possible field names and their formats.  The return is a
// string that is suitable for inclusion SQL Insert statement.  If no
// data is to be set, false is returned.  The keys in $data should be
// named the same as the field names or the corrosponding field will
// not be set.  Also, only the fields found in the $field array will
// be set, not the ones in $data.  The $field array contains arrays
// of two elements, the first being the field name, and the second is
// the format, which can be one of "int", "str", or "bool".  
// The output is in the format: " (n1, n2, ...) VALUES (v1, v2, ...) ".
function GenerateSqlInsert($data, $fields)
{
    $loc = "database.php->GenerateSqlInsert";
    // First make an array that is an intersection of the two inputs.
    $final = array();
    foreach($fields as $f)
    {
        $fn = $f[0];  // Field name
        $ft = $f[1];  // Field type
        if(!isset($data[$fn])) continue;
        if(is_null($data[$fn])) continue;
        $v = $data[$fn];
        $final[] = array("FieldName"=>$fn, "FieldType"=>$ft, "Value"=>$v);
    }
    if(count($final) <= 0) return false;

    // First, list the columns...
    $sql = ' (';
    $c = 0;
    foreach($final as $f)
    {
        if($c != 0) $sql .= ', ';
        $sql .= $f["FieldName"];
        $c++;
    }
    $sql .= ') VALUES (';
    $c = 0;
    foreach($final as $f)
    {
        if($c != 0) $sql .= ', ';
        if($f["FieldType"]      == 'int')  $sql .= intval($f["Value"]);
        else if($f["FieldType"] == 'str')  $sql .= '"' . SqlClean($f["Value"]) . '"';
        else if($f["FieldType"] == 'bool') $sql .= TFstr($f["Value"]);
        else 
        {
            DieWithMsg($loc, "Bad Sql type: " . $f["FieldType"] . 
                       " for field " . $f["FieldName"] . '.');         
        }
        $c++;
    }
    $sql .= ') ';
    return $sql;
}

// --------------------------------------------------------------------
// Cleans an input string of characters that might cause sql injection
// attack and possible fatal errors.  Does not claim to be completely
// hack proof, but should help.  Does the following: replaces all control
// chars, except \n, \t, \r with a space, and replaces " and ' with a 
// space.  If the input is not a string, it is returned as is.
function SqlClean($s)
{
    if(gettype($s) != "string") return $s;
    $okay_chars ="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ0123456789";
    $okay_chars .="~`!@#$%^&*()-_=+[]{}\|;:,.<>/? "; 
    $okay_chars .="\n\r\t";   
    $out = "";
    while(!blank($s) && $s !== false)
    {
        $c = substr($s, 0, 1);
        $s = substr($s, 1);
        $w = stripos($okay_chars, $c);
        if($w === FALSE) $out .= ' ';
        else             $out .= $c;
    }
    return $out;
}

// --------------------------------------------------------------------
// Returns all (or filtered) rows and columns of an sql table.
function GetSqlTable($tablename, $filter = "")
{
    $loc = "databaselib.php->GetSqlTable";
    $sql = "SELECT * FROM " . $tablename;
    if(!empty($filter)) $sql .= ' ' . $filter;
    $result = SqlQuery($loc, $sql);
    $table = array();
    while($row = $result->fetch_assoc()) $table[] = $row;
    return $table;
}

// --------------------------------------------------------------------
// Checks to see if the input is okay for an SQL query.  "Okay" 
// means that it does not contain illegal characters.  The input
// can be a simple string or an array containing mixed types.  True
// is returned if the input is okay, false otherwise.
function IsSqlTextOkay($s)
{
    if(!is_array($s))
    {
        if(SqlClean($s) !== $s) { return false; }
        else return true;
    }
    foreach($s as $t)
    {
        if(SqlClean($t) !== $t) { return false; }
    }
    return true;
}

?>



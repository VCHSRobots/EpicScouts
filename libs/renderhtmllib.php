<?php
// --------------------------------------------------------------------
// renderhtmllib.php -- code to help in rendering html.
//
// Created: 01/02/16 DLB
// --------------------------------------------------------------------

// --------------------------------------------------------------------
// Renders a label/value block as three divs.  One div encapsulates
// the other two.  The encapsulated divs contain the label and the
// value. The divs are given classes and ids by appending _block, _label and
// _value to the $class and $id argement.
function RenderField($class, $id, $caption, $value, $exact=false)
{
    echo '<div id="' . $id . '_block" class="' . $class . '_block">';
    echo '<div id="' . $id . '_label" class="' . $class . '_label">' . $caption . '</div>' . "\n";
    echo '<div id="' . $id . '_value" class="' . $class . '_value">' ;
    if($exact) echo '<pre>' . $value . '</pre></div>' . "\n";
    else       echo $value . '</div>' . "\n";
    echo '</div>' . "\n";
}

// --------------------------------------------------------------------
// Render a Table from arrays.  The way it work is as follows:
//
// Data is prepared by inserting into two arrays: the header and the
// rows.  The header is a flat array of column names. The rows is an
// array of arrays.  Each sub array is one row of data, usually with 
// the same number of elements as the header.  
//
// Each element in the table is given a class name so that you can 
// format the table with css.  For a 4x4 table with header, the
// class names are assigned as follows, where the "ccc" is a name
// you supply when the table is generated.
//
//   ccc_head_1  ccc_head_2  ccc_head_3  ccc_head_4  
//
//   ccc_col_1   ccc_col_2   ccc_col_3   ccc_col_4
//   ccc_col_1   ccc_col_2   ccc_col_3   ccc_col_4
//   ccc_col_1   ccc_col_2   ccc_col_3   ccc_col_4
//   ccc_col_1   ccc_col_2   ccc_col_3   ccc_col_4
// 
// The main class for the table will be ccc.  

function RenderTable($head, $rows, $classname="tbl")
{

	$ncols = count($head);
	echo '<table class="' . $classname . '">' . "\n";
	echo '<tr>';
	$i = 0;
	foreach($head as $h)
	{
		$i++;
		echo '<th class="' . $classname . '_head_' . $i . '">' . $h  . '</th>' . "\n";
	}
	echo '</tr>';

	foreach($rows as $r)
	{
		$n = count($r);
		echo "\n";
		echo '<tr>';
		for($i = 1; $i <= $ncols; $i++) 
		{
			if($i > $n) echo '<tr></tr>';
			else
			{
				echo '<th class="' . $classname . '_col_' . $i . '">' . $r[$i - 1] . '</th>' . "\n";
			}
		}
		echo '</tr>' . "\n";
	}
	echo '</table>' . "\n";
}

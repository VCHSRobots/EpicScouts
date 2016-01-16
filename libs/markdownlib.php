<?php
// --------------------------------------------------------------------
// markdownlib.php -- Turns markdown text into html.
//
// This file makes use of the markdown lib from github.com/michelf/php-markdown.
// as of 1/6/16.
//
// Created: 01/06/16 DLB
// --------------------------------------------------------------------

require_once dirname(__FILE__) . '/markdown/Markdown.inc.php';
use \Michelf\Markdown;

// --------------------------------------------------------------------
// Given markdown text, turns it into html.  A shell div is output
// around the markdown.  You can specifiy the css class and div id.
function MarkdownToHtml($markdown, $class="markdown", $divid="")
{
	$c = "";
	$id = "";
	if(!empty($class)) $c = ' class="' . $class . '"';
	if(!empty($divid)) $id = ' id="' . $divid . '"';

	$html = '<div ' . $c . $id . '>' . "\n";
	$html .= Markdown::defaultTransform($markdown);
	$html .= '</div>' . "\n";
	return $html;
}
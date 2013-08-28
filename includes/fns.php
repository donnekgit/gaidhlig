<?php

function query($sql)
// simplify the query writing
// use this as: $result=query("select * from table");
{
    global $db_handle;
    return pg_query($db_handle,$sql);
}

function drop_existing_table($table)
// Drop the specified table so that it can be recreated.
// or: SELECT * FROM pg_tables WHERE tablename = 'mytable' AND schemaname = 'myschema';
// use true instead of * if preferred
// the and clause does not work in PPA
{
	global $db_handle;
	$sql_exists="select count(*) as count from pg_class where relname = '".$table."'";
	$result_exists=pg_query($db_handle, $sql_exists);
	$row_exists=pg_fetch_object($result_exists);
	if ($row_exists->count > 0)
	{
		$sql_del="drop table $table";
		$result_del=pg_query($db_handle, $sql_del);
		//echo "Table ".$newchafile." already exists";
	}
	else
	{
		//echo "There is no table ".$newchafile;
		//exit;
	}
}

function wordclean($text)
// Make corrections to individual words.
{
    $text=preg_replace("/[:;,!\?\.\(\)\[\]]/u", "", $text);
    return $text;
}

function remove_accent($text)
// Replace accented letters with lowercase non-accented ones.  Note: the u option is required, because these are multibyte characters.
{
	$text=preg_replace("/[âäáàÂ]/u", "a", $text);
	$text=preg_replace("/[êëéè]/u", "e", $text);
	$text=preg_replace("/[îïíì]/u", "i", $text);
	$text=preg_replace("/[ôöóòÔ]/u", "o", $text);
	$text=preg_replace("/[ûüúù]/u", "u", $text);
	$text=preg_replace("/[ŷÿýỳŶ]/u", "y", $text);
	$text=preg_replace("/[ŵẅẃẁŴ]/u", "w", $text);
	return $text;
}

function asp($text)
// Remove aspiration (lenition)
{
    $text=preg_replace("/^b/", "bh", $text);
	$text=preg_replace("/^B/", "Bh", $text);
    $text=preg_replace("/^c/", "ch", $text);
	$text=preg_replace("/^C/", "Ch", $text);
	$text=preg_replace("/^d/", "dh", $text);
	$text=preg_replace("/^D/", "Dh", $text);
	$text=preg_replace("/^f/", "fh", $text);
	$text=preg_replace("/^F/", "Fh", $text);
	$text=preg_replace("/^g/", "gh", $text);
	$text=preg_replace("/^G/", "Gh", $text);
	$text=preg_replace("/^m/", "mh", $text);
	$text=preg_replace("/^M/", "Mh", $text);
    $text=preg_replace("/^p/", "ph", $text);
    $text=preg_replace("/^P/", "Ph", $text);
    $text=preg_replace("/^s/", "sh", $text);
    $text=preg_replace("/^S/", "Sh", $text);
    $text=preg_replace("/^t/", "th", $text);
    $text=preg_replace("/^T/", "Th", $text);
    return $text;
}

function de_asp($text)
// Remove aspiration (lenition)
{
    $text=preg_replace("/^bh/", "b", $text);
	$text=preg_replace("/^Bh/", "B", $text);
    $text=preg_replace("/^ch/", "c", $text);
	$text=preg_replace("/^Ch/", "C", $text);
	$text=preg_replace("/^dh/", "d", $text);
	$text=preg_replace("/^Dh/", "D", $text);
	$text=preg_replace("/^fh/", "f", $text);
	$text=preg_replace("/^Fh/", "F", $text);
	$text=preg_replace("/^gh/", "g", $text);
	$text=preg_replace("/^Gh/", "G", $text);
	$text=preg_replace("/^mh/", "m", $text);
	$text=preg_replace("/^Mh/", "M", $text);
    $text=preg_replace("/^ph/", "p", $text);
    $text=preg_replace("/^Ph/", "P", $text);
    $text=preg_replace("/^sh/", "s", $text);
    $text=preg_replace("/^Sh/", "S", $text);
    $text=preg_replace("/^th/", "t", $text);
    $text=preg_replace("/^Th/", "T", $text);
    return $text;
}

function pre($text)
// Add dh'.  Need to escape the apostrophe.
{
    $text=preg_replace("/^([FfAaÀàOoÒòEeÈèIiÌìUuUù])/", "dh''$1", $text);
    return $text;
}

function n($text)
// Add n-
{
    $text=preg_replace("/^([AaÀàOoÒòEeÈèIiÌìUuUù])/", "n-$1", $text);
    return $text;
}

function t($text)
// Add t-
{
    $text=preg_replace("/^([SsAaÀàOoÒòEeÈèIiÌìUuUù])/", "t-$1", $text);
    return $text;
}

function de_pre($text)
// Remove prefixes.
{
    $text=preg_replace("/^[Dd]h''/", "", $text);
    $text=preg_replace("/^[Dd]''/", "", $text);
    $text=preg_replace("/^[hnt]-/", "", $text);
    return $text;
}

function de_suf($text)
// Remove prefixes.
{
    $text=preg_replace("/-se$/", "", $text);
    $text=preg_replace("/-san$/", "", $text);
    return $text;
}


function adh($text)
// Remove -[e]adh
{
    $text=preg_replace("/a?adh$''/", "", $text);
    return $text;
}


function ch($text)
// Unescape apostrophe in queries.
{
    $text=preg_replace("/''/", "'", $text);
    return $text;
}

function tex_surface($text)
// Escape any characters that annoy TeX.
{
	$text=preg_replace("/_/", "\_", $text);
	$text=preg_replace("/%/", "\%", $text);
	$text=preg_replace("/\&/", "\&", $text);
	$text=preg_replace("/#/", "\#", $text);
	$text=preg_replace("/</", "$<$", $text);
	$text=preg_replace("/>/", "$>$", $text);
	$text=preg_replace("/\+\^/", "+\\textasciicircum~", $text);
	$text=preg_replace("/\+\/\//", "+$//$", $text);
	//$text=preg_replace("/\.\.\./", " \dots ", $text);
	// Substitutions to handle IPA characters - remember to load the TIPA package in the header
	// Valid:
	$text=preg_replace("/ǝ/", "\\textipa{@} ", $text);
	// Invalid (copy-and-pasted characters aren't recognised by the editor)
	$text=preg_replace("/ʧ/", "\\textipa{tS} ", $text);
	$text=preg_replace("/ð/", "\\textipa{D} ", $text);
	$text=preg_replace("/ɛ/", "\\textipa{E} ", $text);
	$text=preg_replace("/ɪ/", "\\textipa{I} ", $text);
	$text=preg_replace("/ŋ/", "\\textipa{N} ", $text);
	$text=preg_replace("/ɔ/", "\\textipa{O} ", $text);
	$text=preg_replace("/ʃ/", "\\textipa{S} ", $text);
	$text=preg_replace("/θ/", "\\textipa{T} ", $text);
	$text=preg_replace("/ɬ/", "\\textbeltl ", $text);
	$text=preg_replace("/ʔ/", "\\textglotstop ", $text);
//ʘ ts; ʣ
	return $text;
}

function tex_auto($text)
// Converts POS tags to \scriptsize
{
	$text=preg_replace("/ /", ".", $text);  // get rid of any spaces in the POS string - should no longer be required; now in write_cgfinished
	$text=preg_replace("/_/", "\_", $text);  // LaTeX no like
	$text=preg_replace("/%/", "\%", $text);  // LaTeX no like
	//$text=preg_replace("/([A-Za-z])(\..*$)/", "$1{\scriptsize $2}", $text);  // Minimise anything after the first dot (ie make the POS-tags following the lexeme smaller).
	//$text=preg_replace("/(([A-Z]|[0-9])([A-Z]|\.).*$)/", "{\scriptsize $1}", $text);  // To make the POS-tags following the lexeme smaller, minimise anything that begins with a digit and cap (3S), a cap and a dot (V.), or two caps (DET).
	//$text=preg_replace("/(I)(\..*$)/", "$1{\scriptsize $2}", $text);  // Handle "I" (1s pron)
    return $text;
}

?>

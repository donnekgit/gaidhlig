<?php

/* 
*********************************************************************
Copyright Kevin Donnelly 2010, 2011.
kevindonnelly.org.uk
This file is part of the Bangor Autoglosser.

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License or the GNU
Affero General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option)
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
and the GNU Affero General Public License along with this program.
If not, see <http://www.gnu.org/licenses/>.
*********************************************************************
*/ 

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
	$utterances="sentences_gloss";
	$words="words";
}

$fp = fopen("outputs/{$words}_cg.txt", "w") or die("Can't create the file");

// FIXME - we need to lowercase and lookup, then surface and lookup, then duplicate (not unknown).
// FIXME - all-digit numbers should be tagged diginum.

$sql=query("select * from $words order by sentence_id, location;");
while ($row=pg_fetch_object($sql))
{
	$surface=pg_escape_string($row->surface);  // Required to allow lookup of words containing an apostrophe.
    $utt=$row->sentence_id;
    $loc=$row->location;
    $place=" {".$utt.",".$loc."} ";
	//echo $row->surface."\n";

    $stream="\"<".$row->surface.">\"\n";  // Each surface form ends in a newline.

	echo $stream;
	fwrite($fp, $stream);

	include("lookups/gla_lookup.php");
}

fclose($fp);

?>

<?php

/* 
*********************************************************************
Copyright Kevin Donnelly 2013.
kevindonnelly.org.uk
This file is part of the Bangor Autoglosser for Gàidhlig.

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
	$words="words";
	$cgfinished="cgfinished";
}

$sql=query("select * from $cgfinished order by sentence, location;");
while ($row=pg_fetch_object($sql))
{
	$enlemma=$row->enlemma.".";
	$pos=($row->pos=='') ? "" : $row->pos.".";
    $extra=($row->extra =='') ? "" : "+".$row->extra."."; 
    $seg=($row->seg =='') ? "" : "+".$row->seg;
	$combined1=$pos.$extra.$seg;
    $combined2=strtoupper($combined1);  // uppercase the POS-tags
    $tags=preg_replace('/\.\+/','+', $combined2);  // remove the dot before a +
    $lemtags=pg_escape_string($enlemma.$tags);
    $lemtags=preg_replace('/\.$/','', $lemtags);  // remove the dot at the end of the string
    
    if ($row->sentence==$utt and $row->location==$loc)
    {
        $auto=$auto."[or]".$lemtags;
        echo "Repeat: ".$row->sentence.":".$row->location.": ".$auto."\n";
    }
    else
    {
        $auto=$lemtags;
        $utt=$row->sentence;
        $loc=$row->location;
        echo "New: ".$utt.":".$loc.": ".$auto."\n";
    }

    // Write them into the words table
    $sql_u=query("update $words set auto='$auto' where sentence_id=$utt and location=$loc;");
}

?>
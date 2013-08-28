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

// This script splits the sentences from store_sentences.php into words and stores them in a db table.

if (!isset($chain))
{
	include("includes/fns.php");
	include("/opt/gaidhlig/config.php");
}

//$sentences="sentences";
//$sentences="sentences_mw";
$sentences="taic_mw";
$words="taic_words";

$thesewords=array();

drop_existing_table($words);

$sql_table = query("
CREATE TABLE $words (
    {$words}_id serial NOT NULL,
    filename character varying(100),
    sentence_id integer,
    location integer,
    surface character varying(100),
    auto character varying(250)
);
");

$sql_pkey = query("
ALTER TABLE ONLY ".$words." ADD CONSTRAINT ".$words."_pk PRIMARY KEY ({$words}_id);
");

$sql=query("select * from $sentences order by id;");
while ($row=pg_fetch_object($sql))
{
	$newutt=trim($row->surface);

    $i=1; 
    
	$surface_words=array_filter(explode(' ', $newutt));

    foreach ($surface_words as $surface_word)
    {
		echo $row->id.": ".$surface_word."\n";
        $surface_word=pg_escape_string(trim($surface_word));
		$clean_word=wordclean($surface_word);
		$clean_word=preg_replace("/_/", " ", $clean_word);
		$sql_w=query("insert into $words (sentence_id, location, surface, filename) values ('$row->id', '$i', '$clean_word', '$row->filename')");
		$i=++$i;
	} 
	
	unset($newutt);
}

?>
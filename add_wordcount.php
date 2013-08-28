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

// Insert the number of words in each sentence in the wordcount field.
// Set up fields word_g and word_e first.

include("includes/fns.php");
include("/opt/gaidhlig/config.php");
$sentences="taic_mw";

$sql=query("select * from $sentences order by id;");
while ($row=pg_fetch_object($sql))
{
	$surface=$row->surface;
	$count_g=str_word_count($surface);
	echo $count_g."\n";
	
	$sql_up=query("update $sentences set word_g=$count_g where id=$row->id;");
}

$sql=query("select * from $sentences order by id;");
while ($row=pg_fetch_object($sql))
{
	$english=$row->english;
	$count_e=str_word_count($english);
	echo $count_e."\n";
	
	$sql_up=query("update $sentences set word_e=$count_e where id=$row->id;");
}






























?>
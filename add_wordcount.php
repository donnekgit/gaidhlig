<?php

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